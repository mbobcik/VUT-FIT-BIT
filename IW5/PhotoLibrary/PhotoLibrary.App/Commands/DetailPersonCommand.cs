using PhotoLibrary.App.ViewModels;
using PhotoLibrary.BL;
using PhotoLibrary.BL.Models;
using PhotoLibrary.BL.Repositories;
using System;
using System.Windows;
using System.Windows.Input;

namespace PhotoLibrary.App.Commands
{
    public class DetailPersonCommand : ICommand
    {
        private readonly PersonRepository personRepository;
        private readonly PersonDetailViewModel _viewModel;
        private readonly IMessenger _messenger;

        public DetailPersonCommand(PersonRepository personRepository, PersonDetailViewModel viewModel,
            IMessenger messenger)
        {
            _messenger = messenger;
            this.personRepository = personRepository;
            _viewModel = viewModel;
        }

        public event EventHandler CanExecuteChanged;

        public bool CanExecute(object parameter)
        {
            return true;
        }

        public void Execute(object parameter)
        {
        }
    }
}