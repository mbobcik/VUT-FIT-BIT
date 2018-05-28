using System;

namespace PhotoLibrary.BL.Messages
{
    public class DeleteAlbumMessage
    {
        public DeleteAlbumMessage(Guid objectId)
        {
            ObjectId = objectId;
        }

        public Guid ObjectId { get; set; }
    }
}
