using System;
using System.Collections.Generic;
using System.Collections.ObjectModel;
using System.ComponentModel;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Input;
using PhotoLibrary.DAL.Entities;
using GalaSoft.MvvmLight;

namespace PhotoLibrary.App.ViewModels
{
    class ItemsListViewModel : ViewModelBase
    {
        public new event PropertyChangedEventHandler PropertyChanged;
        //TODO download all items from DB
        public ObservableCollection<Item> Items { get; } = new ObservableCollection<Item>();

        private Item _selectedItem;

        public Item SelectedItem
        {
            get => _selectedItem;
            set
            {
                _selectedItem = value;
                PropertyChanged?.Invoke(this, new PropertyChangedEventArgs("SelectedItem"));
            }
        }

        public string Name
        {
            get => _selectedItem.Name;
            set
            {
                _selectedItem.Name = value;
                PropertyChanged?.Invoke(this, new PropertyChangedEventArgs("Name"));
            }
        }
       
       // public ObservableCollection<Photo> PhotosOn => new ObservableCollection<Photo>(_selectedItem.Photo);

        public ICommand FindByName { get; set; }
    }
}
