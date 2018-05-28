using PhotoLibrary.DAL.Entities;
using PhotoLibrary.DAL.Entities.Base.Implementation;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;

namespace PhotoLibrary.DAL.Entities
{
    public class PhotoCoordinates : EntityObject
    {
        [Required]
        public Photo Photo { get; set; }
        [Required]
        public ICollection<Coordinates> Coordinates { get; set; } = new List<Coordinates>();
    }
}
