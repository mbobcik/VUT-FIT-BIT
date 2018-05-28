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
    public class PersonListViewModel : ViewModelBase
    {
        private ObservableCollection<PersonListModel> people;
        private readonly PersonRepository personRepository;
        private readonly IMessenger messenger;

        public Guid PhotoId { get; set; }
        public ICommand DetailCommand { get; }
        public ICommand SelectPersonCommand { get; }
        public ICommand MouseDoubleClickCommand { get; }

        public ObservableCollection<PersonListModel> People
        {
            get { return people; }
            set
            {
                if (Equals(value, people)) return;
                people = value;
                OnPropertyChanged();
            }
        }

        public PersonListViewModel(PersonRepository personRepository, IMessenger messenger, Mapper mapper)
        {
            this.personRepository = personRepository;
            this.messenger = messenger;

            DetailCommand = new RelayCommand(PersonDetail);
            SelectPersonCommand = new RelayCommand(PersonSelectionChanged);

            this.messenger.Register<ShowDetailPhotoMessage>(OnLoad);
        }

        private void PersonSelectionChanged(object obj)
        {
            var person = (PersonListModel)obj;
            if (person == null)
            {
                return;
            }
            messenger.Send(new SelectedPersonInPhotoViewMessage() { Id = person.Id });
        }

        private void PersonDetail(object obj)
        {
            
        }

        private void OnLoad(ShowDetailPhotoMessage message)
        {
            PhotoId = message.Id;
            People = new ObservableCollection<PersonListModel>(personRepository.GetAllByPhotoId(PhotoId));
        }

        private void Reload()
        {
            People = new ObservableCollection<PersonListModel>(personRepository.GetAllByPhotoId(PhotoId));
        }
    }
}