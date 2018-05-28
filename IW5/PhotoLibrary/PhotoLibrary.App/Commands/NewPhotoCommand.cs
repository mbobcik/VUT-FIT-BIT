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
    public class NewPhotoCommand : ICommand
    {
        private readonly AlbumRepository _albumRepository;
        private readonly AlbumDetailViewModel _viewModel;
        private readonly IMessenger _messenger;

        public NewPhotoCommand(AlbumRepository albumRepository, AlbumDetailViewModel viewModel,
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
            var photoCreator = new Views.PhotoDetailView();
            _messenger.Send(new SelectedAlbumInAlbumListMessage { Id = _viewModel.Detail.Id });
            _messenger.Send(new NewPhotoMessage());
            photoCreator.ShowDialog();
        }

        public event EventHandler CanExecuteChanged
        {
            add { CommandManager.RequerySuggested += value; }
            remove { CommandManager.RequerySuggested -= value; }
        }
    }
}