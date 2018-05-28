using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using PhotoLibrary.DAL.Entities.Base.Implementation;

namespace PhotoLibrary.DAL.Entities
{
    public class Photo : EntityObject
    {
        [Required]
        public string Name { get; set; }
        [Required]
        public DateTime DateTime { get; set; }
        [Required]
        public FileFormat FileFormat { get; set; }
        [Required]
        public string Path { get; set; }
        public string Description { get; set; }
        public int Width { get; set; }
        public int Height { get; set; }
        public Album Album { get; set; }

        public virtual ICollection<Person> Persons { get; set; } = new List<Person>();
        public virtual ICollection<Item> Items { get; set; } = new List<Item>();
    }
}
