using System;

namespace PhotoLibrary.BL.Models
{
    public class AlbumListModel
    {
        public Guid Id { get; set; }
        public string Name { get; set; }
        public string Description { get; set; }
        public DateTime DateTime { get; set; }
    }
}
