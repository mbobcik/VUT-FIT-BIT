using System;
using System.Collections.Generic;
using System.Configuration;
using System.Data.SqlClient;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PhotoLibrary.BL.Repositories;

namespace PhotoLibrary.Tests
{
    public abstract class TestsBase : IDisposable
    {

        public ICollection<Guid> TestGuidList { get; private set; } = new List<Guid>();
        protected readonly AlbumRepository TestAlbumRepository = new AlbumRepository();

        protected TestsBase()
        {
            // Do "global" initialization here; Called before every test method.
            AddNewGuid();
            CreateDatabaseSnapshot("PhotoLibraryDB","PhotoLibraryDB", Directory.GetCurrentDirectory()+ "\\PhotoLibraryDBSnapshot.ss");
        }

        public void Dispose()
        {
            // Do "global" teardown here; Called after every test method.
            TestGuidList.Clear();
            RevertDatabaseFromSnapshot("PhotoLibraryDB","PhotoLibraryDBSnapshot");
        }

        public Guid AddNewGuid()
        {
            Guid id = Guid.NewGuid();
            
            TestGuidList.Add(id);
            return id;
        }

        private void CreateDatabaseSnapshot(string databaseName, string databaseLogicalName, string snapshotPath)
        {
            //ConfigurationManager.AppSettings["MySetting"]
            using (SqlConnection cnn = new SqlConnection(ConfigurationManager.ConnectionStrings["PhotoLibraryContext"].ToString()))
            {
                using (SqlCommand cmd = new SqlCommand())
                {
                    try
                    {
                        cmd.Connection = cnn;
                        cmd.CommandTimeout = 1000;
                        string snapshotName = System.IO.Path.GetFileNameWithoutExtension(snapshotPath);
                        cmd.CommandText = "CREATE DATABASE " + snapshotName + " ON ( NAME = " + databaseLogicalName + ", FILENAME = '" + snapshotPath + "' ) AS SNAPSHOT OF " + databaseName + ";";

                        cnn.Open();
                        Console.WriteLine("CREATE SNAPSHOT: " + cmd.ExecuteNonQuery().ToString());
                    }
                    catch (Exception ex)
                    {
                        Console.WriteLine(ex.Message);
                    }
                }
                cnn.Close();
            }
        }

        private void RevertDatabaseFromSnapshot(string databaseName, string snapshotName)
        {
            using (SqlConnection cnn = new SqlConnection(ConfigurationManager.ConnectionStrings["PhotoLibraryContext"].ToString()))
            {
                using (SqlCommand cmd = new SqlCommand())
                {
                    try
                    {
                        cmd.Connection = cnn;
                        cmd.CommandTimeout = 1000;
                        cmd.CommandText = "RESTORE DATABASE " + databaseName + " FROM DATABASE_SNAPSHOT = '" + snapshotName + "'; DROP DATABASE " + snapshotName + ";";

                        cnn.Open();
                        Console.WriteLine("REVERT SNAPSHOT: " + cmd.ExecuteNonQuery().ToString());
                    }
                    catch (Exception ex)
                    {
                        Console.WriteLine(ex.Message);
                    }
                }
            }
        }

    }
}
