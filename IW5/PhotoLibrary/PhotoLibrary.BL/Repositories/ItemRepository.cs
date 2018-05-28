using System;
using System.Collections.Generic;
using System.Data.Entity;
using System.Linq;
using PhotoLibrary.BL.Models;
using PhotoLibrary.DAL;
using PhotoLibrary.DAL.Entities;

namespace PhotoLibrary.BL.Repositories
{
    public class ItemRepository
    {
        private Mapper mapper = new Mapper();

        public List<ItemListModel> GetAllByPhotoId(Guid photoId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                return context.Items.Where(s => s.Photos.Any(c => c.Id == photoId)).Select(mapper.EntityToListModel).ToList();
            }
        }

        public ItemDetailModel GetById(Guid id)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var item = context.Items.FirstOrDefault(a => a.Id == id);

                return mapper.EntityToDetailModel(item);
            }
        }

        public ItemDetailModel Insert(ItemDetailModel itemDetail, Guid photoId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var itemEntity = mapper.DetailModelToEntity(itemDetail);
                itemEntity.Id = Guid.NewGuid();
                itemEntity.Photos.Add(context.Photos.First(a => a.Id == photoId));

                context.Items.Add(itemEntity);
                context.SaveChanges();

                return mapper.EntityToDetailModel(itemEntity);
            }
        }

        public void Update(ItemDetailModel itemDetail)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var itemEntity = context.Items.First(a => a.Id == itemDetail.Id);

                itemEntity.Name = itemDetail.Name;

                context.SaveChanges();
            }
        }
        public void Delete(Guid itemId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                foreach (var item in context.Items)
                {
                    if (!item.Id.Equals(itemId)) continue;
                    context.Items.Remove(item);
                    break;
                }

                context.SaveChanges();
            }
        }

    }
}