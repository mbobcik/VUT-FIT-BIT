using System;
using System.Collections.Generic;
using System.Data.Entity;
using System.Linq;
using System.Security.Cryptography.X509Certificates;
using PhotoLibrary.BL.Models;
using PhotoLibrary.DAL;
using PhotoLibrary.DAL.Entities;

namespace PhotoLibrary.BL.Repositories
{
    public class AlbumRepository
    {
        private Mapper mapper = new Mapper();

        public List<AlbumListModel> GetAll()
        {
            using (var context = new PhotoLibraryDbContext())
            {
                return context.Albums.Select(mapper.EntityToListModel).ToList();
            }
        }

        public AlbumDetailModel GetById(Guid id)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var album = context.Albums.FirstOrDefault(a => a.Id == id);

                return mapper.EntityToDetailModel(album);
            }
        }

        public AlbumDetailModel Insert(AlbumDetailModel albumDetail)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var albumEntity = mapper.DetailModelToEntity(albumDetail);
                albumEntity.Id = Guid.NewGuid();                

                context.Albums.Add(albumEntity);
                context.SaveChanges();

                return mapper.EntityToDetailModel(albumEntity);
            }
        }

        public void Update(AlbumDetailModel albumDetail)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var albumEntity = context.Albums.First(a => a.Id == albumDetail.Id);

                albumEntity.Name = albumDetail.Name;
                albumEntity.DateTime = albumDetail.DateTime;
                albumEntity.Description = albumDetail.Description;

                context.SaveChanges();
            }
        }

        public void Delete(Guid albumId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var album = context.Albums.First(a => a.Id.Equals(albumId));
                if (album != null)
                {
                    context.Albums.Remove(album);
                }

                context.SaveChanges();
            }
        }
    }
}
