using PhotoLibrary.DAL.Entities.Base.Implementation;
using System.ComponentModel.DataAnnotations;

namespace PhotoLibrary.DAL.Entities
{
    public class Coordinates : EntityObject
    {
        [Required]
        public int X1 { get; set; }
        [Required]
        public int Y1 { get; set; }
        [Required]
        public int X2 { get; set; }
        [Required]
        public int Y2 { get; set; }
    }
}
