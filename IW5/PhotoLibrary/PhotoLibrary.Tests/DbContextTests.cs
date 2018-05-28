using System;
using System.Configuration;
using System.Collections.Generic;
using System.Data.Entity;
using System.IO;
using System.Linq;
using PhotoLibrary;
using PhotoLibrary.BL;
using PhotoLibrary.BL.Models;
using PhotoLibrary.BL.Repositories;
using Xunit;
using PhotoLibrary.DAL;
using PhotoLibrary.DAL.Entities;

namespace PhotoLibrary.Tests
{
    public class DbContextTests :TestsBase
    {
        [Fact]
        public void Database_Exists()
        {
            using (var DBContext = new PhotoLibraryDbContext())
            {
                Assert.True(DBContext.Database.Exists());
            }
        }

        private readonly AlbumRepository albumRepo = new AlbumRepository();
        private readonly ItemRepository itemRepo = new ItemRepository();
        private readonly PersonRepository personRepo = new PersonRepository();
        private readonly PhotoRepository photoRepo = new PhotoRepository();
        private Mapper mapper = new Mapper();

        [Fact]
        public void GetAll_Album_NotNull()
        {
            var albums = albumRepo.GetAll();
            Assert.NotNull(albums);
        }

        [Fact]
        public void GetById_Album_NotNull()
        {                                                   //Known GUID
            var album = albumRepo.GetById(new Guid("80bb3892-008d-4cd1-9775-420664c11297"));
            Assert.NotNull(album);
        }

        [Fact]
        public void GetById_Album_IsNull()
        {                                           //Unknown GUID
            var album = albumRepo.GetById(TestGuidList.ElementAt(0));
            Assert.Null(album);
        }

        [Fact]
        public void Insert_Album_NoPhotos()
        {
            //Set up
            AlbumDetailModel album = new AlbumDetailModel()
            {
                DateTime = DateTime.Now,
                Description = "Description",
                Id = TestGuidList.ElementAt(0),
                Name = "TmpAlbum",
                Photos = null
            };

            //Unit Test
             albumRepo.Insert(album);

            //Assert
            Assert.NotNull(albumRepo.GetById(TestGuidList.ElementAt(0)));

            //Tear down
            
        }

        [Fact]
        public void Delete_Album_NoPhotos()
        {
            //set up
            AlbumDetailModel album = new AlbumDetailModel()
            {
                DateTime = DateTime.Now,
                Description = "Description",
                Id = TestGuidList.ElementAt(0),
                Name = "TmpAlbum",
                Photos = null
            };
            albumRepo.Insert(album);
            
            //unit test
            albumRepo.Delete(TestGuidList.ElementAt(0));

            //Assert
            Assert.Null(albumRepo.GetById(TestGuidList.ElementAt(0)));
        }

        [Fact]
        public void Update_Album_NoPhotos()
        {
            DateTime dt = DateTime.MaxValue;
            //set up
            AlbumDetailModel album = new AlbumDetailModel()
            {
                DateTime = dt,
                Description = "Description",
                Id = TestGuidList.ElementAt(0),
                Name = "TmpAlbum",
                Photos = null
            };
            albumRepo.Insert(album);

            AlbumDetailModel updatedAlbum = new AlbumDetailModel()
            {
                DateTime = dt,
                Description = "Very descriptive description",
                Id = TestGuidList.ElementAt(0),
                Name = "new TmpAlbum",
                Photos = null
            };

            //unit test
            albumRepo.Update(updatedAlbum);

            //Assert
            var albumFromDB = albumRepo.GetById(TestGuidList.ElementAt(0));
            Assert.Equal(updatedAlbum.Description, albumFromDB.Description);

            //tear down
            
        }

        [Fact]
        public void DeleteAlbum_With_Photos()
        {
            //set up
            
            Photo photo = new Photo
            {
                DateTime =  DateTime.Now,
                Name = "testPhoto",
                FileFormat = FileFormat.gif,
                Path ="My/path/to/file",
                Id = AddNewGuid()
            };
            AlbumDetailModel album = new AlbumDetailModel()
            {
                DateTime = DateTime.Now,
                Description = "Description",
                Id = TestGuidList.ElementAt(0),
                Name = "TmpAlbum1",
                Photos = new List<PhotoListModel> { mapper.EntityToListModel(photo) }
            };
            photo.Album = mapper.DetailModelToEntity(album);
            albumRepo.Insert(album);
            photoRepo.Insert(mapper.EntityToDetailModel(photo), album.Id);

            //unit test
            albumRepo.Delete(TestGuidList.ElementAt(0));

            //Assert
            Assert.Null(albumRepo.GetById(TestGuidList.ElementAt(0)));
            Assert.Null(photoRepo.GetById(TestGuidList.ElementAt(1)));
        }

        [Fact]
        public void Delete_Photo()
        {
            Photo photo = new Photo
            {
                DateTime = DateTime.Now,
                Name = "testPhoto",
                FileFormat = FileFormat.gif,
                Path = "My/path/to/file",
                Id = AddNewGuid()
            };
            AlbumDetailModel album = new AlbumDetailModel()
            {
                DateTime = DateTime.Now,
                Description = "Description",
                Id = TestGuidList.ElementAt(0),
                Name = "TmpAlbum1",
                Photos = new List<PhotoListModel> {  mapper.EntityToListModel(photo) }
            };
            photo.Album = mapper.DetailModelToEntity(album);
            albumRepo.Insert(album);
            photoRepo.Insert(mapper.EntityToDetailModel(photo), album.Id);
            
            photoRepo.Delete(photo.Id);

            Assert.Null(photoRepo.GetById(photo.Id));
            Assert.NotNull(albumRepo.GetById(album.Id));
        }

        [Fact]
        public void Delete_Person()
        {
            Guid personGuid = new Guid("5cf00bf2-0ef7-4620-a119-c647656d8f36");           
            Guid photoGuid = new Guid("1495d2c7-3984-43df-bba2-2f08e2d24ef4");
            Guid albumGuid = new Guid("80bb3892-008d-4cd1-9775-420664c11297");

            personRepo.Delete(personGuid);

            Assert.Null(personRepo.GetById(personGuid));
            Assert.NotNull(photoRepo.GetById(photoGuid));
            Assert.NotNull(albumRepo.GetById(albumGuid));            
        }


        [Fact]
        public void Delete_Item()
        {
            Guid itemGuid = new Guid("fd0d7440-7b49-40b0-bd18-20b0433c619a");
            Guid photoGuid = new Guid("e9918553-28d2-4626-b921-98a678dc2fcf");
            Guid albumGuid = new Guid("80bb3892-008d-4cd1-9775-420664c11297");

            itemRepo.Delete(itemGuid);

            Assert.Null(personRepo.GetById(itemGuid));
            Assert.NotNull(photoRepo.GetById(photoGuid));
            Assert.NotNull(albumRepo.GetById(albumGuid));
        }

    }
}
