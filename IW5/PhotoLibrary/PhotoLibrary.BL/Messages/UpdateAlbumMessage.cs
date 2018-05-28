using System;
using PhotoLibrary.BL.Models;

namespace PhotoLibrary.BL.Messages
{
    public class UpdateAlbumMessage
    {
        public AlbumDetailModel Model { get; set; }
        public UpdateAlbumMessage(AlbumDetailModel model)
        {
            Model = model;
        }
    }
}
