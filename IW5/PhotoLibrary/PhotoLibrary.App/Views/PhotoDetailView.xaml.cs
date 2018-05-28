using System;
using System.Windows.Controls;
using System.Windows;
using Microsoft.Win32;

namespace PhotoLibrary.App.Views
{
    public partial class PhotoDetailView : Window
    {
        public PhotoDetailView()
        {
            InitializeComponent();
        }

        private void Button_Click(object sender, RoutedEventArgs e)
        {
            // Create OpenFileDialog

            Microsoft.Win32.OpenFileDialog dlg = new Microsoft.Win32.OpenFileDialog();



            // Set filter for file extension and default file extension

            dlg.DefaultExt = ".jpg";

            dlg.Filter = "Pictures|*.jpg|Pictures|*.png|Pictures|*.gif";

            //\nPictures (.png)|*.png\nPictures (.gif)|*.gif



            // Display OpenFileDialog by calling ShowDialog method

            Nullable<bool> result = dlg.ShowDialog();



            // Get the selected file name and display in a TextBox

            if (result == true)

            {

                // Open document

                string filename = dlg.FileName;

                //FileNameTextBox.Text = filename;
                FilePathTextBox.Text = filename;
                if (filename.EndsWith(".jpg"))
                {
                    FileFormatTextBlock.Text = "jpg";
                }
                else if (filename.EndsWith(".png"))
                {
                    FileFormatTextBlock.Text = "png";
                }
                else if (filename.EndsWith(".gif"))
                {
                    FileFormatTextBlock.Text = "gif";
                }

            }
        }
    }
}
