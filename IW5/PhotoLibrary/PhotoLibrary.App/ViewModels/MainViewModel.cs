using System.Windows;
using System.Windows.Input;
using PhotoLibrary.App.Commands;
using PhotoLibrary.BL;
using PhotoLibrary.BL.Messages;
using PhotoLibrary.BL.Repositories; 

namespace PhotoLibrary.App.ViewModels
{
    public class MainViewModel : ViewModelBase
    {
        private readonly IMessenger messenger;

        public string Name { get; set; } = "Not loaded";
        public ICommand CreateAlbumCommand { get; set; }

        public MainViewModel(IMessenger messenger)
        {
            this.messenger = messenger;
            CreateAlbumCommand = new RelayCommand(AddNewAlbum);
        }

        private void AddNewAlbum()
        {
            messenger.Send(new NewAlbumMessage());
        }

        public void OnLoad()
        {
        }
    }
}