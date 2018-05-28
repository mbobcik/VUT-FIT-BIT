using PhotoLibrary.DAL.Entities;
using System;
using System.Collections.Generic;

namespace PhotoLibrary.BL.Models
{
    public class AlbumDetailModel
    {
        public Guid Id { get; set; }
        public string Name { get; set; }
        public DateTime DateTime { get; set; } = DateTime.Now;
        public string Description { get; set; }

        public virtual ICollection<PhotoListModel> Photos { get; set; }
    }
}
