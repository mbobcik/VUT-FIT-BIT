using System;
using System.Windows;
using System.Windows.Input;

namespace PhotoLibrary.App.Commands
{
    public class RelayCommand : ICommand
    {
        private readonly Action<object> executeAction;
        private readonly Func<object, bool> canExecuteAction;

        public RelayCommand(Action<object> executeAction, Func<object, bool> canExecuteAction = null)
        {
            this.executeAction = executeAction;
            this.canExecuteAction = canExecuteAction;
        }

        public RelayCommand(Action executeAction, Func<bool> canExecuteAction = null)
            : this(p => executeAction(), p => canExecuteAction?.Invoke() ?? true)
        {

        }

        public bool CanExecute(object parameter)
        {
            var a = canExecuteAction?.Invoke(parameter) ?? true;
            return a;
        }

        public void Execute(object parameter)
        {
            executeAction?.Invoke(parameter);
        }

        public void RaiseCanExecuteChanged()
        {
            CanExecuteChanged?.Invoke(this, EventArgs.Empty);
        }

        public event EventHandler CanExecuteChanged;
    }
}