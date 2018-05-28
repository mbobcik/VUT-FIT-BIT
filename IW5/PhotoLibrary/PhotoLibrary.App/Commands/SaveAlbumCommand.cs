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
    public class SaveAlbumCommand : ICommand
    {
        private readonly AlbumRepository _albumRepository;
        private readonly AlbumDetailViewModel _viewModel;
        private readonly IMessenger _messenger;

        public SaveAlbumCommand(AlbumRepository albumRepository, AlbumDetailViewModel viewModel,
            IMessenger messenger)
        {
            _messenger = messenger;
            _albumRepository = albumRepository;
            _viewModel = viewModel;
        }

        public bool CanExecute(object parameter)
        {
            var detail = parameter as AlbumDetailModel;
            if (detail == null) return false;
            return true;
        }

        public void Execute(object parameter)
        {
            var detail = parameter as AlbumDetailModel;
            if (detail == null)
            {
                return;
            }
            if (detail.Id != Guid.Empty)
            {
                _albumRepository.Update(detail);
            }
            else
            {
                _viewModel.Detail = _albumRepository.Insert(detail);
            }
            _messenger.Send(new UpdateAlbumMessage(detail));
        }

        public event EventHandler CanExecuteChanged
        {
            add { CommandManager.RequerySuggested += value; }
            remove { CommandManager.RequerySuggested -= value; }
        }
    }
}