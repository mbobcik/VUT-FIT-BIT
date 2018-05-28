using System;
using System.Collections.ObjectModel;
using System.Linq;
using System.Windows;
using System.Windows.Input;
using PhotoLibrary.App.Commands;
using PhotoLibrary.App.ViewModels;
using PhotoLibrary.BL;
using PhotoLibrary.BL.Messages;
using PhotoLibrary.BL.Models;
using PhotoLibrary.BL.Repositories;

namespace PhotoLibrary.App.ViewModels
{
    public class AlbumListViewModel : ViewModelBase
    {
        private readonly AlbumRepository albumRepository;
        private readonly IMessenger messenger;
        private readonly Mapper mapper;

        public ObservableCollection<AlbumListModel> Albums { get; set; } = new ObservableCollection<AlbumListModel>();

        public ICommand SelectAlbumCommand { get; }
        public ICommand OnLoadCommand { get; set; }

        public AlbumListViewModel(AlbumRepository albumRepository, IMessenger messenger, Mapper mapper)
        {
            this.albumRepository = albumRepository;
            this.messenger = messenger;
            this.mapper = mapper;

            SelectAlbumCommand = new RelayCommand(AlbumSelectionChanged);
            OnLoadCommand = new RelayCommand(OnLoad);

            this.messenger.Register<DeleteAlbumMessage>(DeleteAlbumMessageRecieved);
            this.messenger.Register<UpdateAlbumMessage>(UpdateAlbumMessageReceived);
        }

        private void UpdateAlbumMessageReceived(UpdateAlbumMessage albumMessage)
        {
            var existingAlbum = Albums.SingleOrDefault(album => album.Id == albumMessage.Model.Id);
            var newAlbum = mapper.DetailModelToListModel(albumMessage.Model);
            if (existingAlbum == null)
            {
                Albums.Add(newAlbum);
            }
            else
            {
                var index = Albums.IndexOf(existingAlbum);
                Albums[index] = newAlbum;
            }
            OnLoad();
        }

        public void OnLoad()
        {
            Albums.Clear();
            var albums = albumRepository.GetAll();
            foreach (var album in albums)
            {
                Albums.Add(album);
            }
        }

        public void AlbumSelectionChanged(object parameter)
        {
            if (parameter is AlbumListModel album)
            {
                if (album == null)
                {
                    return;
                }
                messenger.Send(new SelectedAlbumMessage { Id = album.Id });
                messenger.Send(new SelectedAlbumInAlbumListMessage() { Id = album.Id });
            }
        }

        private void DeleteAlbumMessageRecieved(DeleteAlbumMessage message)
        {
            var deletedAlbum = Albums.FirstOrDefault(r => r.Id == message.ObjectId);
            if (deletedAlbum != null)
            {
                Albums.Remove(deletedAlbum);
            }
        }
    }
}
