using PhotoLibrary.App.ViewModels;
using PhotoLibrary.BL;
using PhotoLibrary.BL.Messages;
using PhotoLibrary.BL.Models;
using PhotoLibrary.BL.Repositories;
using System;
using System.Windows;
using System.Windows.Input;

namespace PhotoLibrary.App.Commands
{
    public class SavePhotoCommand : ICommand
    {
        private readonly PhotoRepository _photoRepository;
        private readonly PhotoDetailViewModel _viewModel;
        private readonly IMessenger _messenger;

        public SavePhotoCommand(PhotoRepository photoRepository, PhotoDetailViewModel viewModel,
            IMessenger messenger)
        {
            _messenger = messenger;
            _photoRepository = photoRepository;
            _viewModel = viewModel;
        }

        public bool CanExecute(object parameter)
        {
            
            var detail = parameter as PhotoDetailModel;

            if (detail == null) return false;
            return true;
        }

        public void Execute(object parameter)
        {
            var detail = parameter as PhotoDetailModel;
            if (detail == null)
            {
                return;
            }
            if (detail.Id != Guid.Empty)
            {
                _photoRepository.Update(detail);
            }
            else
            {
                _viewModel.Detail = _photoRepository.Insert(detail, _viewModel.AlbumId);
            }
            _messenger.Send(new UpdatePhotoMessage(detail));
        }

        public event EventHandler CanExecuteChanged
        {
            add { CommandManager.RequerySuggested += value; }
            remove { CommandManager.RequerySuggested -= value; }
        }
    }
}