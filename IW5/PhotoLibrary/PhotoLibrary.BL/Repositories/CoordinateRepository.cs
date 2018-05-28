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
    public class CoordinateRepository
    {
        private Mapper mapper = new Mapper();

        public List<CoordinateDetailModel> GetAllByPhotoId(Guid photoId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var photoCoordinates = context.PhotoCoordinates.Where(p => p.Photo.Id == photoId)
                    .Select(mapper.EntityToDetailModel).ToList();

                List < CoordinateDetailModel > result = new List<CoordinateDetailModel>();
                foreach (var detailModel in photoCoordinates)
                {
                    result.InsertRange(0,detailModel.Coordinates);
                }

                return result ;
            }
        }

        public CoordinateDetailModel GetById(Guid id)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var detailModel = context.Coordinates.FirstOrDefault(p => p.Id == id);
                return mapper.EntityToDetailModel(detailModel);
            }
        }

        public CoordinateDetailModel Insert(CoordinateDetailModel coordModel, Guid photoCoordId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var coord = mapper.DetailModelToEntity(coordModel);
                coord.Id = Guid.NewGuid();

                var photoCoord = context.PhotoCoordinates.First(p => p.Id == photoCoordId);
                photoCoord.Coordinates.Add(coord);
                
                context.SaveChanges();

                return mapper.EntityToDetailModel(coord);
            }
        }

        public void Update(CoordinateDetailModel coordDetail)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var photoCoord = context.Coordinates.First(p => p.Id == coordDetail.Id);

                photoCoord = mapper.DetailModelToEntity(coordDetail);
                context.SaveChanges();
            }
        }

        public void Delete(Guid coordId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                foreach (var coord in context.Coordinates)
                {
                    if (!coord.Id.Equals(coordId)) continue;
                    context.Coordinates.Remove(coord);
                    break;
                }

                context.SaveChanges();
            }
        }
    }
}
