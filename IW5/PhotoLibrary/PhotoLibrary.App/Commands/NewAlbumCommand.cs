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
    public class NewAlbumCommand : ICommand
    {
        private readonly AlbumRepository _albumRepository;
        private readonly AlbumDetailViewModel _viewModel;
        private readonly IMessenger _messenger;

        public NewAlbumCommand(AlbumRepository albumRepository, AlbumDetailViewModel viewModel,
            IMessenger messenger)
        {
            _messenger = messenger;
            _albumRepository = albumRepository;
            _viewModel = viewModel;
        }

        public bool CanExecute(object parameter)
        {
            
            return true;
        }

        public void Execute(object parameter)
        {
            var albumCreator = new Views.PhotoDetailView();
            if (_viewModel.Detail == null)
            {
                _messenger.Send(new NewAlbumMessage());
                _messenger.Send(new SelectedAlbumInAlbumListMessage { Id = _viewModel.Detail.Id });
            }
            else
            {
                _messenger.Send(new SelectedAlbumInAlbumListMessage { Id = _viewModel.Detail.Id });
                _messenger.Send(new NewAlbumMessage());
                //albumCreator.ShowDialog();
            }

        }

        public event EventHandler CanExecuteChanged
        {
            add { CommandManager.RequerySuggested += value; }
            remove { CommandManager.RequerySuggested -= value; }
        }
    }
}