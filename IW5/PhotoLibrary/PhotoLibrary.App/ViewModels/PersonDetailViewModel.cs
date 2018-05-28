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
    public class PersonDetailViewModel : ViewModelBase
    {
        public readonly PersonRepository personRepository;
        private readonly IMessenger _messenger;
        private PersonDetailModel _detail;

        public PersonDetailModel Detail
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

        public ICommand SaveCommand { get; }
        public ICommand PersonDetailCommand { get; }

        // TODO List of Persons and list of items from DB

        public PersonDetailViewModel(PersonRepository personRepository, IMessenger messenger)
        {
            this.personRepository = personRepository;
            _messenger = messenger;

            _messenger.Register<SelectedPersonInPhotoViewMessage>(OnLoad);
            _messenger.Register<ShowDetailPersonMessage>(ShowDetailPersonMessageReceived);
        }

        private void ShowDetailPersonMessageReceived(ShowDetailPersonMessage message)
        {
            Detail = personRepository.GetById(message.Id);
        }

        private void OnLoad(SelectedPersonInPhotoViewMessage message)
        {
            Detail = personRepository.GetById(message.Id);
        }
    }
}