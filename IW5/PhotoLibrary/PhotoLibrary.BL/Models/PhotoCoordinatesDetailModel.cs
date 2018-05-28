using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace PhotoLibrary.BL.Models
{
    public class PhotoCoordinatesDetailModel
    {
        public Guid Id { get; set; }
        public ICollection<CoordinateDetailModel> Coordinates { get; set; }
        public PhotoDetailModel Photo{ get; set; }
    }
}
