using PhotoLibrary.DAL.Entities;
using System;
using System.Collections.Generic;

namespace PhotoLibrary.BL.Models
{
    public class PersonDetailModel
    {
        public Guid Id { get; set; }
        public string FirstName { get; set; }
        public string Surname { get; set; }
        public ICollection<Coordinates> Coordinates { get; set; }
    }
}
