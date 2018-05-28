using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;
using PhotoLibrary.DAL.Entities.Base.Implementation;

namespace PhotoLibrary.DAL.Entities
{
    public class Album : EntityObject
    {
        [Required]
        public string Name { get; set; }
        [Required]
        public DateTime DateTime { get; set; }
        public string Description { get; set; }

        public virtual ICollection<Photo> Photos { get; set; } = new List<Photo>();
    }
}
