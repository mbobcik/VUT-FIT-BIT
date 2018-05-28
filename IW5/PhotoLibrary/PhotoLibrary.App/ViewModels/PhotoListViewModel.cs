using System;
using System.Collections.Generic;
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
    public class PhotoListViewModel : ViewModelBase
    {
        private ObservableCollection<PhotoListModel> _photos;
        private readonly PhotoRepository _photoRepository;
        private readonly IMessenger _messenger;

        public Guid AlbumId { get; set; }
        public ICommand SelectPhotoCommand { get; }

        public ObservableCollection<PhotoListModel> Photos
        {
            get { return _photos; }
            set
            {
                if (Equals(value, _photos)) return;
                _photos = value;
                OnPropertyChanged();
            }
        }

        public PhotoListViewModel(PhotoRepository photoRepository, IMessenger messenger, Mapper mapper)
        {
            _photoRepository = photoRepository;
            _messenger = messenger;

            SelectPhotoCommand = new RelayCommand(PhotoSelectionChanged);

            _messenger.Register<SelectedAlbumInAlbumListMessage>(OnLoad);
            _messenger.Register<UpdatePhotoMessage>((p) => Reload());
            _messenger.Register<DeletePhotoMessage>(DeletePhoto);
            _messenger.Register<NewAlbumMessage>((p) => EmptyList());
        }


        private void EmptyList()
        {
            Photos = null;
        }

        private void OnLoad(SelectedAlbumInAlbumListMessage message)
        {
            AlbumId = message.Id;
            Photos = new ObservableCollection<PhotoListModel>(_photoRepository.GetAllByAlbumId(message.Id));
        }

        private void Reload()
        {
            Photos = new ObservableCollection<PhotoListModel>(_photoRepository.GetAllByAlbumId(AlbumId));
        }

        private void PhotoSelectionChanged(object parameter)
        {
            var photo = (PhotoListModel)parameter;
            if (photo == null)
            {
                return;
            }
            _messenger.Send(new SelectedPhotoInAlbumViewMessage() { Id = photo.Id });
        }

        private void DeletePhoto(DeletePhotoMessage message)
        {
            var result = MessageBox.Show("Naozaj chcete vymazať túto fotku ?", "Vymazanie fotky", MessageBoxButton.YesNo, MessageBoxImage.Question);
            if (result == MessageBoxResult.No)
            {
                return;
            }
            _photoRepository.Delete(message.PhotoId);
            var photo = Photos.FirstOrDefault(r => r.Id == message.PhotoId);
            if (photo != null)
            {
                Photos.Remove(photo);
            }
        }
    }
}