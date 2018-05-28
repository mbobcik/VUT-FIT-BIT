using System;

namespace PhotoLibrary.BL.Messages
{
    public class SelectedPhotoInAlbumMessage
    {
        public Guid AlbumId { get; set; }
        public Guid PhotoId { get; set; }
    }
}