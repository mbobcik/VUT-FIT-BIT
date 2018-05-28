using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PhotoLibrary.DAL.Entities;
using PhotoLibrary.BL.Models;

namespace PhotoLibrary.BL
{
    public class Mapper
    {
        public AlbumDetailModel EntityToDetailModel(Album album)
        {
            if (album == null)
            {
                return null;
            }
            return new AlbumDetailModel()
            {
                Id = album.Id,
                Name = album.Name,
                DateTime = album.DateTime,
                Description = album.Description
            };
        }
        public AlbumListModel EntityToListModel(Album album)
        {
            if (album == null)
            {
                return null;
            }
            return new AlbumListModel()
            {
                Id = album.Id,
                Name = album.Name,
                Description = album.Description,
                DateTime = album.DateTime
            };
        }
        public Album DetailModelToEntity(AlbumDetailModel album)
        {
            if (album == null)
            {
                return null;
            }
            return new Album()
            {
                Id = album.Id,
                Name = album.Name,
                DateTime = album.DateTime,
                Description = album.Description
            };
        }

        public AlbumListModel DetailModelToListModel(AlbumDetailModel album)
        {
            if (album == null)
            {
                return null;
            }
            return new AlbumListModel
            {
                Id = album.Id,
                Name = album.Name,
                DateTime = album.DateTime,
                Description = album.Description
            };
        }

        public PhotoDetailModel EntityToDetailModel(Photo photo)
        {
            if (photo == null)
            {
                return null;
            }
            return new PhotoDetailModel()
            {
                Id = photo.Id,
                Name = photo.Name,
                DateTime = photo.DateTime,
                FileFormat = photo.FileFormat,
                Path = photo.Path,
                Description = photo.Description,
                Width = photo.Width,
                Height = photo.Height
            };
        }
        public PhotoListModel EntityToListModel(Photo photo)
        {
            if (photo == null)
            {
                return null;
            }
            return new PhotoListModel()
            {
                Id = photo.Id,
                Name = photo.Name,
                Path = photo.Path
            };
        }
        public Photo DetailModelToEntity(PhotoDetailModel photo)
        {
            if (photo == null)
            {
                return null;
            }
            return new Photo()
            {
                Id = photo.Id,
                Name = photo.Name,
                DateTime = photo.DateTime,
                FileFormat = photo.FileFormat,
                Path = photo.Path,
                Description = photo.Description,
                Width = photo.Width,
                Height = photo.Height
            };
        }

        public ItemDetailModel EntityToDetailModel(Item item)
        {
            if (item == null)
            {
                return null;
            }
            return new ItemDetailModel()
            {
                Id = item.Id,
                Name = item.Name
            };
        }
        public ItemListModel EntityToListModel(Item item)
        {
            if (item == null)
            {
                return null;
            }
            return new ItemListModel()
            {
                Id = item.Id,
                Name = item.Name
            };
        }
        public Item DetailModelToEntity(ItemDetailModel item)
        {
            if (item == null)
            {
                return null;
            }
            return new Item()
            {
                Id = item.Id,
                Name = item.Name
            };
        }
        public PersonDetailModel EntityToDetailModel(Person person)
        {
            if (person == null)
            {
                return null;
            }
            return new PersonDetailModel()
            {
                Id = person.Id,
                FirstName = person.FirstName,
                Surname = person.Surname
            };
        }
        public PersonListModel EntityToListModel(Person person)
        {
            if (person == null)
            {
                return null;
            }
            return new PersonListModel()
            {
                Id = person.Id,
                FirstName = person.FirstName,
                Surname = person.Surname
            };
        }
        public Person DetailModelToEntity(PersonDetailModel person)
        {
            if (person == null)
            {
                return null;
            }
            return new Person()
            {
                Id = person.Id,
                FirstName = person.FirstName,
                Surname = person.Surname
            };
        }

        public CoordinateDetailModel EntityToDetailModel(Coordinates coord)
        {
            if (coord == null)
            {
                return null;
            }

            return new CoordinateDetailModel()
            {
                Id = coord.Id,

                X1 = coord.X1,
                Y1 = coord.Y1,
                X2 = coord.X2,
                Y2 = coord.Y2
            };
        }

        public Coordinates DetailModelToEntity(CoordinateDetailModel coord)
        {
            if (coord == null)
            {
                return null;
            }

            return new Coordinates()
            {
                Id = coord.Id,

                X1 = coord.X1,
                Y1 = coord.Y1,
                X2 = coord.X2,
                Y2 = coord.Y2
            };
        }

        public PhotoCoordinatesDetailModel EntityToDetailModel(PhotoCoordinates coords)
        {
            if (coords == null)
            {
                return null;
            }

            ICollection < CoordinateDetailModel > coordinatesCollection = new List<CoordinateDetailModel>();
            foreach (var coordinateEntity in coords.Coordinates)
            {
                coordinatesCollection.Add(EntityToDetailModel(coordinateEntity));
            }

            return new PhotoCoordinatesDetailModel()
            {
                Id = coords.Id,
                Photo = EntityToDetailModel(coords.Photo),
                Coordinates = coordinatesCollection
            };
        }

        public PhotoCoordinates DetailModelToEntity(PhotoCoordinatesDetailModel photoCoordModel)
        {
            if (photoCoordModel == null)
            {
                return null;
            }

            ICollection<Coordinates> coordinatesCollection = new List<Coordinates>();

            foreach (var coordModel in photoCoordModel.Coordinates)
            {
                coordinatesCollection.Add(DetailModelToEntity(coordModel));
            }

            return new PhotoCoordinates()
            {
                Id = photoCoordModel.Id,
                Photo = DetailModelToEntity(photoCoordModel.Photo),
                Coordinates = coordinatesCollection
            };
        }
    }
}
