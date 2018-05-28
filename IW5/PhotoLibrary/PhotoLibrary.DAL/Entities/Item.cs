using PhotoLibrary.DAL.Entities.Base.Implementation;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;

namespace PhotoLibrary.DAL.Entities
{
    public class Item : PhotoObject
    {
        [Required]
        public string Name { get; set; }
    }
}
