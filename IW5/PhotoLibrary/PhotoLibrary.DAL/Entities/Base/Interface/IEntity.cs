using System;

namespace PhotoLibrary.DAL.Entities.Base.Interface
{
    public interface IEntity
    {
        Guid Id { get; }
    }
}