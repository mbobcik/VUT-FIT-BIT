using System;
using System.Collections.ObjectModel;
using System.Linq;
using System.Windows.Input;
using System.Windows;
using PhotoLibrary.App.Commands;
using PhotoLibrary.BL;
using PhotoLibrary.BL.Messages;
using PhotoLibrary.BL.Models;
using PhotoLibrary.BL.Repositories;

namespace PhotoLibrary.App.ViewModels
{
    public class AlbumDetailViewModel : ViewModelBase
    {
        private readonly AlbumRepository albumRepository;
        private readonly IMessenger messenger;
        private AlbumDetailModel detail;

        public AlbumDetailModel Detail
        {
            get { return detail; }
            set
            {
                if (Equals(value, Detail))
                    return;

                detail = value;
                OnPropertyChanged();
            }
        }

        private Guid PhotoId { get; set; }
        public bool State { get; set; }
        public ICommand SaveAlbumCommand { get; }
        public ICommand DeleteAlbumCommand { get; }
        public ICommand NewPhotoCommand { get; }
        public ICommand EditPhotoCommand { get; }
        public ICommand DeletePhotoCommand { get; }
        public ICommand DetailPhotoCommand { get; }
        public ICommand ShowPhotoDetailCommand { get; }

        public AlbumDetailViewModel(AlbumRepository albumRepository, IMessenger messenger)
        {
            this.albumRepository = albumRepository;
            this.messenger = messenger;

            SaveAlbumCommand = new SaveAlbumCommand(albumRepository, this, messenger);
            DeleteAlbumCommand = new RelayCommand(DeleteAlbum);

            ShowPhotoDetailCommand = new RelayCommand(ShowPhotoDetail);
            DetailPhotoCommand = new RelayCommand(DetailPhotoView);
            NewPhotoCommand = new NewPhotoCommand(albumRepository, this, messenger);
            EditPhotoCommand = new RelayCommand(EditPhoto);
            DeletePhotoCommand = new RelayCommand(DeletePhoto);

            this.messenger.Register<SelectedAlbumMessage>(SelectedAlbum);
            this.messenger.Register<NewAlbumMessage>(NewAlbumMessageRecieved);
            this.messenger.Register<SelectedPhotoInAlbumViewMessage>(SelectedPhoto);

        }

        private void DetailPhotoView(object obj)
        {
            var photo = new Views.PhotoDetailInDetailView();
            if (PhotoId == Guid.Empty)
                return;
            
            messenger.Send(new ShowDetailPhotoMessage { Id = PhotoId });
            photo.ShowDialog();
        }

        private void DeleteAlbum()
        {
            if (Detail.Id != Guid.Empty)
            {
                var result = MessageBox.Show("Naozaj chcete vymazať tento album ? ", "Vymazanie albumu", MessageBoxButton.YesNo, MessageBoxImage.Question);
                if (result == MessageBoxResult.No)
                {
                    return;
                }
                var detailId = Detail.Id;
                Detail = new AlbumDetailModel();
                albumRepository.Delete(detailId);
                messenger.Send(new DeleteAlbumMessage(detailId));
            }
        }

        private void DeletePhoto()
        {
            if (PhotoId == Guid.Empty)
                return;
            messenger.Send(new DeletePhotoMessage { AlbumId = Detail.Id, PhotoId = PhotoId });
        }

        private void ShowPhotoDetail()
        {
            if (PhotoId == Guid.Empty)
                return;

            var photoDetail = new Views.PhotoInAlbumView();
            messenger.Send(new SelectedPhotoInAlbumMessage { AlbumId = Detail.Id, PhotoId = PhotoId });
            photoDetail.ShowDialog();
        }

        private void EditPhoto()
        {
            var photo = new Views.PhotoDetailView { Window = { Title = "Upraviť fotku" } };
            if (PhotoId == Guid.Empty)
                return;
            
            messenger.Send(new EditPhotoMessage { Id = PhotoId });
            photo.ShowDialog();
        }

        private void SelectedAlbum(SelectedAlbumMessage message)
        {
            Detail = albumRepository.GetById(message.Id);
        }

        private void NewAlbumMessageRecieved(NewAlbumMessage message)
        {
            Detail = new AlbumDetailModel();
        }

        private void SelectedPhoto(SelectedPhotoInAlbumViewMessage message)
        {
            PhotoId = message.Id;
        }
    }
}
