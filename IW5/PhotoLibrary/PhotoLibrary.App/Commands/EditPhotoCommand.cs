using PhotoLibrary.App.ViewModels;
using PhotoLibrary.BL;
using PhotoLibrary.BL.Models;
using PhotoLibrary.BL.Repositories;
using System;
using System.Windows.Input;

namespace PhotoLibrary.App.Commands
{
    public class EditPhotoCommand : ICommand
    {
        private readonly PhotoRepository _photoRepository;
        private readonly PhotoDetailViewModel _viewModel;
        private readonly IMessenger _messenger;

        public EditPhotoCommand(PhotoRepository photoRepository, PhotoDetailViewModel viewModel,
            IMessenger messenger)
        {
            _messenger = messenger;
            _photoRepository = photoRepository;
            _viewModel = viewModel;
        }

        public event EventHandler CanExecuteChanged;

        public bool CanExecute(object parameter)
        {
            throw new NotImplementedException();
        }

        public void Execute(object parameter)
        {
            throw new NotImplementedException();
        }
    }
}