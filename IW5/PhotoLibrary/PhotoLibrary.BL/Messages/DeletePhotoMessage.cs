using System;

namespace PhotoLibrary.BL.Messages
{
    public class DeletePhotoMessage
    {
        public Guid PhotoId { get; set; }
        public Guid AlbumId { get; set; }

    }
}