using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using ParserLib;
using System.IO;

namespace UnitTestConsole
{
    class Program
    {

        static void Main(string[] args)
        {
            //pokud je zadan aspon jeden parametr
            if (args.Length >= 1)
            {//a pokud je parametr spravny
                if (args[0] == "-noinput" || args[0] == "-n" || args[0] == "-noInput")
                {//pak vypise vystup na stdout bez zadani vstupu
                    Tests.TestAll();
                    return;
                }
                else
                {//jinak se pokusi vytvorit/otevrit soubor se jmenem parametru a presmeruje do nej vystup testu
                    try
                    {
                        string dir = Directory.GetCurrentDirectory();
                        dir = dir + "/" + args[0];
                        using (StreamWriter sw = new StreamWriter(dir, false, Encoding.Unicode))
                        {
                            Console.SetOut(sw);
                            Tests.TestAll();
                        }
                        return;
                    }
                    catch (Exception e)
                    {
                        Console.WriteLine("Error: {0}", e);
                    }
                }
            }// if(args.Length >=1)
            //pokud neni zadan zadny parametr, vypise vystup testu na stdout a ceka na zmacknuti klavesy
            //(aby konzole s vystupem jen neproblikla)
            Tests.TestAll();
            Console.WriteLine("Press random key to exit...");
            Console.ReadLine();
        }//Main()
    }//class
}//namespace
