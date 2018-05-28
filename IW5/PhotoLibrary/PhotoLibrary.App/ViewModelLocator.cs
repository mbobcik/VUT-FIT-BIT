using System;
using PhotoLibrary.App.ViewModels;
using PhotoLibrary.BL;
using PhotoLibrary.BL.Repositories;

namespace PhotoLibrary.App
{
    public class ViewModelLocator
    {
        private readonly Messenger messenger = new Messenger();
        private readonly AlbumRepository albumRepository = new AlbumRepository();
        private readonly ItemRepository itemRepository = new ItemRepository();
        private readonly PersonRepository personRepository = new PersonRepository();
        private readonly PhotoRepository photoRepository = new PhotoRepository();
        private readonly PhotoCoordinateRepository coordinateRepository = new PhotoCoordinateRepository();
        private readonly Mapper mapper = new Mapper();

        public MainViewModel MainViewModel => CreateMainViewModel();
        public AlbumListViewModel AlbumListViewModel => CreateAlbumListViewModel();
        public AlbumDetailViewModel AlbumDetailViewModel => CreateAlbumDetailViewModel();
        public PhotoListViewModel PhotoListViewModel => CreatePhotoListViewModel();
        public PhotoDetailViewModel PhotoDetailViewModel => CreatePhotoDetailViewModel();
        public PersonListViewModel PersonListViewModel => CreatePersonListViewModel();
        public PersonDetailViewModel PersonDetailViewModel => CreatePersonDetailViewModel();

        private MainViewModel CreateMainViewModel()
        {
            return new MainViewModel(messenger);
        }

        private AlbumDetailViewModel CreateAlbumDetailViewModel()
        {
            return new AlbumDetailViewModel(albumRepository, messenger);
        }
        private AlbumListViewModel CreateAlbumListViewModel()
        {
            return new AlbumListViewModel(albumRepository, messenger, mapper);
        }

        private PhotoDetailViewModel CreatePhotoDetailViewModel()
        {
            return new PhotoDetailViewModel(photoRepository, messenger);
        }
        private PhotoListViewModel CreatePhotoListViewModel()
        {
            return new PhotoListViewModel(photoRepository, messenger, mapper);
        }

        private PersonListViewModel CreatePersonListViewModel()
        {
            return new PersonListViewModel(personRepository, messenger, mapper);
        }

        private PersonDetailViewModel CreatePersonDetailViewModel()
        {
            return new PersonDetailViewModel(personRepository, messenger);
        }
    }
}