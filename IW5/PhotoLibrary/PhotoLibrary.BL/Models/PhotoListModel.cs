using PhotoLibrary.DAL.Entities;
using System;

namespace PhotoLibrary.BL.Models
{
    public class PhotoListModel
    {
        public Guid Id { get; set; }
        public string Name { get; set; }
        public string Path { get; set; }
    }
}
