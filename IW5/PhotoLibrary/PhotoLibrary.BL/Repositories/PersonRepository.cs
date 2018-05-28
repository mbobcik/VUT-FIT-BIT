using System;
using System.Collections.Generic;
using System.Data.Entity;
using System.Linq;
using PhotoLibrary.BL.Models;
using PhotoLibrary.DAL;
using PhotoLibrary.DAL.Entities;

namespace PhotoLibrary.BL.Repositories
{
    public class PersonRepository
    {
        private Mapper mapper = new Mapper();

        public List<PersonListModel> GetAllByPhotoId(Guid photoId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                return context.Persons.Where(s => s.Photos.Any(c => c.Id == photoId)).Select(mapper.EntityToListModel).ToList();
            }
        }

        public PersonDetailModel GetById(Guid id)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var person = context.Persons.FirstOrDefault(a => a.Id == id);

                return mapper.EntityToDetailModel(person);
            }
        }

        public PersonDetailModel Insert(PersonDetailModel personDetail, Guid photoId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var personEntity = mapper.DetailModelToEntity(personDetail);
                personEntity.Id = Guid.NewGuid();
                personEntity.Photos.Add(context.Photos.First(a => a.Id == photoId));

                context.Persons.Add(personEntity);
                context.SaveChanges();

                return mapper.EntityToDetailModel(personEntity);
            }
        }

        public void Update(PersonDetailModel personDetail)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                var personEntity = context.Persons.First(a => a.Id == personDetail.Id);

                personEntity.FirstName = personDetail.FirstName;
                personEntity.Surname = personDetail.Surname;

                context.SaveChanges();
            }
        }
        public void Delete(Guid personId)
        {
            using (var context = new PhotoLibraryDbContext())
            {
                foreach (var person in context.Persons)
                {
                    if (!person.Id.Equals(personId)) continue;
                    context.Persons.Remove(person);
                    break;
                }

                context.SaveChanges();
            }
        }

    }
}