using PhotoLibrary.BL.Models;

namespace PhotoLibrary.BL.Messages
{
    public class UpdatePhotoMessage
    {
        public PhotoDetailModel Model { get; set; }
        public UpdatePhotoMessage(PhotoDetailModel model)
        {
            Model = model;
        }
    }
}
