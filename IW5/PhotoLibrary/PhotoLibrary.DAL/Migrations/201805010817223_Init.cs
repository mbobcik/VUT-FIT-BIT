namespace PhotoLibrary.DAL.Migrations
{
    using System;
    using System.Data.Entity.Migrations;
    
    public partial class Init : DbMigration
    {
        public override void Up()
        {
            CreateTable(
                "dbo.Albums",
                c => new
                    {
                        Id = c.Guid(nullable: false),
                        Name = c.String(nullable: false),
                        DateTime = c.DateTime(nullable: false),
                        Description = c.String(),
                    })
                .PrimaryKey(t => t.Id);
            
            CreateTable(
                "dbo.Photos",
                c => new
                    {
                        Id = c.Guid(nullable: false),
                        Name = c.String(nullable: false),
                        DateTime = c.DateTime(nullable: false),
                        FileFormat = c.Int(nullable: false),
                        Path = c.String(nullable: false),
                        Description = c.String(),
                        Width = c.Int(nullable: false),
                        Height = c.Int(nullable: false),
                        Album_Id = c.Guid(),
                    })
                .PrimaryKey(t => t.Id)
                .ForeignKey("dbo.Albums", t => t.Album_Id)
                .Index(t => t.Album_Id);
            
            CreateTable(
                "dbo.Items",
                c => new
                    {
                        Id = c.Guid(nullable: false),
                        Name = c.String(nullable: false),
                    })
                .PrimaryKey(t => t.Id);
            
            CreateTable(
                "dbo.PhotoCoordinates",
                c => new
                    {
                        Id = c.Guid(nullable: false),
                        Photo_Id = c.Guid(nullable: false),
                        Item_Id = c.Guid(),
                        Person_Id = c.Guid(),
                    })
                .PrimaryKey(t => t.Id)
                .ForeignKey("dbo.Photos", t => t.Photo_Id, cascadeDelete: true)
                .ForeignKey("dbo.Items", t => t.Item_Id)
                .ForeignKey("dbo.People", t => t.Person_Id)
                .Index(t => t.Photo_Id)
                .Index(t => t.Item_Id)
                .Index(t => t.Person_Id);
            
            CreateTable(
                "dbo.Coordinates",
                c => new
                    {
                        Id = c.Guid(nullable: false),
                        X1 = c.Int(nullable: false),
                        Y1 = c.Int(nullable: false),
                        X2 = c.Int(nullable: false),
                        Y2 = c.Int(nullable: false),
                        PhotoCoordinates_Id = c.Guid(),
                    })
                .PrimaryKey(t => t.Id)
                .ForeignKey("dbo.PhotoCoordinates", t => t.PhotoCoordinates_Id)
                .Index(t => t.PhotoCoordinates_Id);
            
            CreateTable(
                "dbo.People",
                c => new
                    {
                        Id = c.Guid(nullable: false),
                        FirstName = c.String(nullable: false),
                        Surname = c.String(nullable: false),
                    })
                .PrimaryKey(t => t.Id);
            
            CreateTable(
                "dbo.ItemPhotoes",
                c => new
                    {
                        Item_Id = c.Guid(nullable: false),
                        Photo_Id = c.Guid(nullable: false),
                    })
                .PrimaryKey(t => new { t.Item_Id, t.Photo_Id })
                .ForeignKey("dbo.Items", t => t.Item_Id, cascadeDelete: true)
                .ForeignKey("dbo.Photos", t => t.Photo_Id, cascadeDelete: true)
                .Index(t => t.Item_Id)
                .Index(t => t.Photo_Id);
            
            CreateTable(
                "dbo.PersonPhotoes",
                c => new
                    {
                        Person_Id = c.Guid(nullable: false),
                        Photo_Id = c.Guid(nullable: false),
                    })
                .PrimaryKey(t => new { t.Person_Id, t.Photo_Id })
                .ForeignKey("dbo.People", t => t.Person_Id, cascadeDelete: true)
                .ForeignKey("dbo.Photos", t => t.Photo_Id, cascadeDelete: true)
                .Index(t => t.Person_Id)
                .Index(t => t.Photo_Id);
            
        }
        
        public override void Down()
        {
            DropForeignKey("dbo.PersonPhotoes", "Photo_Id", "dbo.Photos");
            DropForeignKey("dbo.PersonPhotoes", "Person_Id", "dbo.People");
            DropForeignKey("dbo.PhotoCoordinates", "Person_Id", "dbo.People");
            DropForeignKey("dbo.ItemPhotoes", "Photo_Id", "dbo.Photos");
            DropForeignKey("dbo.ItemPhotoes", "Item_Id", "dbo.Items");
            DropForeignKey("dbo.PhotoCoordinates", "Item_Id", "dbo.Items");
            DropForeignKey("dbo.PhotoCoordinates", "Photo_Id", "dbo.Photos");
            DropForeignKey("dbo.Coordinates", "PhotoCoordinates_Id", "dbo.PhotoCoordinates");
            DropForeignKey("dbo.Photos", "Album_Id", "dbo.Albums");
            DropIndex("dbo.PersonPhotoes", new[] { "Photo_Id" });
            DropIndex("dbo.PersonPhotoes", new[] { "Person_Id" });
            DropIndex("dbo.ItemPhotoes", new[] { "Photo_Id" });
            DropIndex("dbo.ItemPhotoes", new[] { "Item_Id" });
            DropIndex("dbo.Coordinates", new[] { "PhotoCoordinates_Id" });
            DropIndex("dbo.PhotoCoordinates", new[] { "Person_Id" });
            DropIndex("dbo.PhotoCoordinates", new[] { "Item_Id" });
            DropIndex("dbo.PhotoCoordinates", new[] { "Photo_Id" });
            DropIndex("dbo.Photos", new[] { "Album_Id" });
            DropTable("dbo.PersonPhotoes");
            DropTable("dbo.ItemPhotoes");
            DropTable("dbo.People");
            DropTable("dbo.Coordinates");
            DropTable("dbo.PhotoCoordinates");
            DropTable("dbo.Items");
            DropTable("dbo.Photos");
            DropTable("dbo.Albums");
        }
    }
}
