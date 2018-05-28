using System.Data.Entity;
using PhotoLibrary.DAL.Entities;

namespace PhotoLibrary.DAL
{
    public class PhotoLibraryDbContext : DbContext
    {
        public IDbSet<Album> Albums { get; set; }
        public IDbSet<Photo> Photos { get; set; }
        public IDbSet<Person> Persons { get; set; }
        public IDbSet<Item> Items { get; set; }
        public IDbSet<Coordinates> Coordinates { get; set; }
        public IDbSet<PhotoCoordinates> PhotoCoordinates { get; set; }

        public PhotoLibraryDbContext() : base("PhotoLibraryContext") { }

        public override int SaveChanges()
        {
            return base.SaveChanges();
        }
    }
}
