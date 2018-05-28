using System;
using System.Collections.Generic;
using System.Data.Entity;
using System.Linq;
using PhotoLibrary.BL.Models;
using PhotoLibrary.DAL;
using PhotoLibrary.DAL.Entities;

namespace PhotoLibrary.BL.Repositories
{
    public class PhotoRepository
    {
        private Mapper mapper = new Mapper();

        public List<PhotoListModel> GetAllByAlbumId(Guid albumId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                return context.Photos.Where(p => p.Album.Id == albumId).Select(mapper.EntityToListModel).ToList();
            }
        }

        public PhotoDetailModel GetById(Guid id)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var album = context.Photos.FirstOrDefault(a => a.Id == id);

                return mapper.EntityToDetailModel(album);
            }
        }

        public PhotoDetailModel Insert(PhotoDetailModel photoDetail, Guid albumId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var photoEntity = mapper.DetailModelToEntity(photoDetail);
                photoEntity.Id = Guid.NewGuid();
                photoEntity.Album = context.Albums.First(a => a.Id == albumId);

                context.Photos.Add(photoEntity);
                context.SaveChanges();

                return mapper.EntityToDetailModel(photoEntity);
            }
        }

        public void Update(PhotoDetailModel photoDetail)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var photoEntity = context.Photos.First(a => a.Id == photoDetail.Id);

                photoEntity.Name = photoDetail.Name;
                photoEntity.DateTime = photoDetail.DateTime;
                photoEntity.FileFormat = photoDetail.FileFormat;
                photoEntity.Description = photoDetail.Description;
                photoEntity.Path = photoDetail.Path;
                photoEntity.Width = photoDetail.Width;
                photoEntity.Height = photoDetail.Height;

                context.SaveChanges();
            }
        }
        public void Delete(Guid photoId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                foreach (var photo in context.Photos)
                {
                    if (!photo.Id.Equals(photoId)) continue;
                    context.Photos.Remove(photo);
                    break;
                }

                context.SaveChanges();
            }
        }

    }
}