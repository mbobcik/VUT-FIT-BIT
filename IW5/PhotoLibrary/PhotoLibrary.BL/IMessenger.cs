﻿using System;

namespace PhotoLibrary.BL
{
    public interface IMessenger
    {
        void Register<TMessage>(Action<TMessage> action);
        void Send<TMessage>(TMessage message);
        void UnRegister<TMessage>(Action<TMessage> action);
    }
}