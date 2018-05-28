using PhotoLibrary.DAL.Entities;
using System;
using System.Collections.Generic;

namespace PhotoLibrary.BL.Models
{
    public class PhotoDetailModel
    {
        public Guid Id { get; set; }
        public string Name { get; set; }
        public DateTime DateTime { get; set; } = DateTime.Now;
        public FileFormat FileFormat { get; set; }
        public string Path { get; set; }
        public string Description { get; set; }
        public int Width { get; set; }
        public int Height { get; set; }
        public Guid AlbumId { get; set; }

        public virtual ICollection<PersonListModel> Persons { get; set; }
        public virtual ICollection<ItemListModel> Items { get; set; }
    }
}
