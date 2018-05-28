using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PhotoLibrary.BL.Models;
using PhotoLibrary.DAL;
using PhotoLibrary.DAL.Entities;

namespace PhotoLibrary.BL.Repositories
{
    public class PhotoCoordinateRepository
    {
        private Mapper mapper = new Mapper();

        public List<PhotoCoordinatesDetailModel> GetAllByPhotoId(Guid photoId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var detailModel = context.PhotoCoordinates.Where(p => p.Photo.Id == photoId)
                    .Select(mapper.EntityToDetailModel).ToList();
                
                return detailModel;
            }
        }

        public PhotoCoordinatesDetailModel GetById(Guid id)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var detailModel = context.PhotoCoordinates.FirstOrDefault(p => p.Id == id);
                return mapper.EntityToDetailModel(detailModel);
            }
        }

        public PhotoCoordinatesDetailModel Insert(PhotoCoordinatesDetailModel coordModel, Guid photoId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var photoCoord = mapper.DetailModelToEntity(coordModel);
                photoCoord.Id = Guid.NewGuid();
                photoCoord.Photo = context.Photos.First(p => p.Id == photoId);
                context.PhotoCoordinates.Add(photoCoord);
                context.SaveChanges();

                return mapper.EntityToDetailModel(photoCoord);
            }
        }

        public void Update(PhotoCoordinatesDetailModel coordDetail)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var photoCoord = context.PhotoCoordinates.First(p => p.Id == coordDetail.Id);

                photoCoord = mapper.DetailModelToEntity(coordDetail);
                context.SaveChanges();
            }
        }

        public void Delete(Guid coordId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                foreach (var photoCoord in context.Photos)
                {
                    if (!photoCoord.Id.Equals(coordId)) continue;
                    context.Photos.Remove(photoCoord);
                    break;
                }

                context.SaveChanges();
            }
        }
    }
}
