using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PhotoLibrary.DAL.Entities;
using PhotoLibrary.DAL.Entities.Base.Implementation;

namespace PhotoLibrary.DAL.Entities
{
    public class PhotoObject : EntityObject
    {
        public virtual ICollection<Photo> Photos { get; set; } = new List<Photo>();
        public virtual ICollection<PhotoCoordinates> PhotoCoordinates { get; set; } = new List<PhotoCoordinates>();
    }
}
