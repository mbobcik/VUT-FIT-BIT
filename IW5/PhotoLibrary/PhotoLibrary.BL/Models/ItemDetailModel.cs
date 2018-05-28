using PhotoLibrary.DAL.Entities;
using System;
using System.Collections.Generic;

namespace PhotoLibrary.BL.Models
{
    public class ItemDetailModel
    {
        public Guid Id { get; set; }
        public string Name { get; set; }
        public ICollection<Coordinates> Coordinates { get; set; }
    }
}
