using PhotoLibrary.DAL.Entities.Base.Implementation;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations;

namespace PhotoLibrary.DAL.Entities
{
    public class Person : PhotoObject
    {
        [Required]
        public string FirstName { get; set; }
        [Required]
        public string Surname { get; set; }
    }
}
