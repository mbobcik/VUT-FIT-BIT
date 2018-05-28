using System.Collections.Generic;

namespace PhotoLibrary.DAL.Migrations
{
    using PhotoLibrary.DAL.Entities;
    using System;
    using System.Data.Entity.Migrations;
    using System.Linq;

    internal sealed class Configuration : DbMigrationsConfiguration<PhotoLibraryDbContext>
    {
        public Configuration()
        {
            AutomaticMigrationsEnabled = false;
        }

        protected override void Seed(PhotoLibraryDbContext context)
        {

            /////////////////Coordinates
            var coorThomasFootball = new Coordinates
            {
                Id = new Guid("b8d9825f-b232-48d6-9416-ad4aa5b250a8"),
                X1 = 10, Y1 = 20,
                X2 = 60, Y2 = 80
            };

            var coorThomasMoon = new Coordinates
            {
                Id = new Guid("581628b9-0b98-46e3-a5b4-743c1284ed3a"),
                X1 = 510, Y1 = 520,
                X2 = 570, Y2 = 590
            };

            var coorItemBallOne = new Coordinates
            {
                Id = new Guid("02a95f3d-a002-43a4-84fa-14c3a57a45e3"),
                X1 = 100, Y1 = 200,
                X2 = 110, Y2 = 210
            };

            var coorItemBallTwo = new Coordinates
            {
                Id = new Guid("9a82bba7-7eac-4af8-bb75-900187e3432c"),
                X1 = 400, Y1 = 600,
                X2 = 410, Y2 = 610
            };

            var coorItemMoon = new Coordinates{
                Id = new Guid("3f93bc83-130b-4355-9e3a-6fb8e8d80b21"),
                X1 = 100, Y1 = 100,
                X2 = 400, Y2 = 400
            };

            /////////////////////Photos
            var photoMoon = new Photo
            {
                Id = new Guid("1495d2c7-3984-43df-bba2-2f08e2d24ef4"),
                Name = "Moon",
                DateTime = DateTime.Now,
                FileFormat = FileFormat.jpg,
                Path = @"C:\Data\football.jpg"
            };

            
            var photoFootball = new Photo
            {
                Id = new Guid("e9918553-28d2-4626-b921-98a678dc2fcf"),
                Name = "Football",
                DateTime = DateTime.Now,
                FileFormat = FileFormat.jpg,
                Path = @"C:\Data\football.jpg"
            };

            //////////////////////PhotoCoordinates
            var photoCorFootball = new PhotoCoordinates
            {
                Id = new Guid("b9336091-db49-4d2d-a4b5-616380e73143"),
                Photo = photoFootball,
                Coordinates = { coorItemBallOne, coorItemBallTwo }
            };

            var photoCorMoon = new PhotoCoordinates
            {
                Id = new Guid("e852d046-1a43-48ef-96f0-17f05aea4fb4"),
                Photo = photoMoon,
                Coordinates = { coorItemMoon }
            };


            var photoCorThomasMoon = new PhotoCoordinates
            {
                Id = new Guid("64719eeb-5d63-49e4-b2ab-41a88a6f59b5"),
                Photo = photoMoon,
                Coordinates = { coorThomasMoon }
            };

            var photoCorThomasFootball = new PhotoCoordinates
            {
                Id = new Guid("4b3d9d10-ada3-44a1-bbe8-f1b04f06d4eb"),
                Photo = photoFootball,
                Coordinates = { coorThomasFootball }
            };

            ////////////////////////Items
            var itemBall = new Item
            {
                Id = new Guid("fd0d7440-7b49-40b0-bd18-20b0433c619a"),
                Name = "Ball",
                PhotoCoordinates = { photoCorFootball }
            };

            var itemMoon = new Item
            {
                Id = new Guid("922e42e5-2217-4b4d-a3f6-867d180cecc3"),
                Name = "Moon",
                PhotoCoordinates = { photoCorMoon }
            };

            ///////////////////////////Persons
            var thomasPerson = new Person
            {
                Id = new Guid("5cf00bf2-0ef7-4620-a119-c647656d8f36"),
                FirstName = "Thomas",
                Surname = "Digger",
                PhotoCoordinates = { photoCorThomasFootball, photoCorThomasMoon }
            };

            ///////////////////////////Albums
            var albumSummer = new Album
            {
                Id = new Guid("80bb3892-008d-4cd1-9775-420664c11297"),
                Name = "Summer",
                DateTime = DateTime.Now,
                Description = "Summer 2018",
                Photos = { photoMoon, photoFootball }
            };

            // Adding Coordinates to DB
            context.Coordinates.AddOrUpdate(coordin => coordin.Id, coorItemBallOne, coorItemBallTwo,coorItemMoon,coorThomasFootball,coorThomasMoon);
            
            //Adding Items to DB
            context.Items.AddOrUpdate(item => item.Id, itemBall,itemMoon);

            //Adding persons to DB
            context.Persons.AddOrUpdate(item => item.Id, thomasPerson);
            context.SaveChanges();

            itemBall = context.Items.First(item => item.Id == itemBall.Id);
            itemMoon = context.Items.First(item => item.Id == itemMoon.Id);
            thomasPerson = context.Persons.First(person => person.Id == thomasPerson.Id);

            //Adding items/persons to photos
            photoFootball.Items.Add(itemBall);
            photoFootball.Persons.Add(thomasPerson);
            photoMoon.Items.Add(itemMoon);
            photoMoon.Persons.Add(thomasPerson);

            // Add reference to albums
            photoFootball.Album = albumSummer;
            photoMoon.Album = albumSummer;

            //Adding photos to DB
            context.Photos.AddOrUpdate(photo => photo.Id, photoFootball, photoMoon);
            
            // Add album to DB
            context.Albums.AddOrUpdate(album => album.Id, albumSummer);
        }
    }
}
