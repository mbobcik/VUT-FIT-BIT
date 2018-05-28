using System;
using PhotoLibrary.DAL.Entities.Base.Interface;

namespace PhotoLibrary.DAL.Entities.Base.Implementation
{
    public abstract class EntityObject : IEntity
    {
        public Guid Id { get; set; } = Guid.NewGuid();
    }
}
