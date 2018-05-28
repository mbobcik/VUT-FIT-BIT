using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.Windows;
using System.Windows.Input;
using PhotoLibrary.App.Commands;
using PhotoLibrary.App.ViewModels;
using PhotoLibrary.BL;
using PhotoLibrary.BL.Messages;
using PhotoLibrary.BL.Models;
using PhotoLibrary.BL.Repositories;
using PhotoLibrary.DAL.Entities;

namespace PhotoLibrary.App.ViewModels
{
    public class PhotoDetailViewModel : ViewModelBase
    {
        public readonly PersonRepository personRepository;
        private readonly PhotoRepository _photoRepository;
        private readonly IMessenger _messenger;
        private PhotoDetailModel _detail;
        public ObservableCollection<PersonListModel> People;

        public PhotoDetailModel Detail
        {
            get { return _detail; }
            set
            {
                if (Equals(value, Detail))
                    return;

                _detail = value;
                OnPropertyChanged();
            }
        }

        public Guid AlbumId { get; set; }
        public Guid PersonId { get; set; }
        public ICommand SaveCommand { get; }
        public ICommand PersonDetailCommand { get; }

        // TODO List of Persons and list of items from DB

        public PhotoDetailViewModel(PhotoRepository photoRepository, IMessenger messenger)
        {
            _photoRepository = photoRepository;
            _messenger = messenger;
            SaveCommand = new SavePhotoCommand(photoRepository, this, messenger);
            PersonDetailCommand = new RelayCommand(PersonDetail);

            _messenger.Register<SelectedAlbumInAlbumListMessage>(OnLoad);
            _messenger.Register<NewPhotoMessage>(NewPhotoMessageRecieved);
            _messenger.Register<EditPhotoMessage>(EditPhotoMessageRecieved);
            _messenger.Register<ShowDetailPhotoMessage>(ShowDetailPhotoMessageReceived);
            _messenger.Register<SelectedPersonInPhotoViewMessage>(SelectedPerson);
        }

        private void SelectedPerson(SelectedPersonInPhotoViewMessage obj)
        {
            PersonId = obj.Id;
        }

        private void PersonDetail(object obj)
        {
            var person = new Views.PersonDetailView();
            if (Detail.Id == Guid.Empty)
                return;

            _messenger.Send(new ShowDetailPersonMessage { Id =  PersonId });
            person.ShowDialog();
        }

        private void ShowDetailPhotoMessageReceived(ShowDetailPhotoMessage message)
        {
            Detail = _photoRepository.GetById(message.Id);
        }

        private void OnLoad(SelectedAlbumInAlbumListMessage message)
        {
            AlbumId = message.Id;
        }

        private void NewPhotoMessageRecieved(NewPhotoMessage message)
        {
            Detail = new PhotoDetailModel();
        }

        private void EditPhotoMessageRecieved(EditPhotoMessage message)
        {
            Detail = _photoRepository.GetById(message.Id);
        }


    }
}