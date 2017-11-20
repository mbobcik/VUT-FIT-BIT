using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using ParserLib;

namespace UnitTestConsole
{
    class Tests
    {
        public static void TestAll()
        {
            string notPassed = "";
            Console.WriteLine("##################\nTest MathLib.fact(double a)");
            if (!factorialTest())
                notPassed += "Neprošel factorialTest\n";
            Console.WriteLine("##################\nTest MathLib.exp(double a)");
            if (!expTest())
                notPassed += "Neprošel expTest\n";
            Console.WriteLine("##################\nTest MathLib.declog(double a)");
            if (!declogTest())
                notPassed += "Neprošel declogTest\n";
            Console.WriteLine("##################\nTest MathLib.ln(double a)");
            if (!lnTest())
                notPassed += "Neprošel lnTest\n";
            Console.WriteLine("##################\nTest MathLib.log(double a, double b)");
            if (!logTest())
                notPassed += "Neprošel logTest\n";
            Console.WriteLine("##################\nTest MathLib.abs(double a)");
            if (!absTest())
                notPassed += "Neprošel absTest\n";
            Console.WriteLine("##################\nTest MathLib.cotg(double a)");
            if (!cotgTest())
                notPassed += "Neprošel cotgTest\n";
            Console.WriteLine("##################\nTest MathLib.cotgr(double a)");
            if (!cotgrTest())
                notPassed += "Neprošel cotgrTest\n";
            Console.WriteLine("##################\nTest MathLib.tan(double a)");
            if (!tanTest())
                notPassed += "Neprošel tanTest\n";
            Console.WriteLine("##################\nTest MathLib.tanr(double a)");
            if (!tanrTest())
                notPassed += "Neprošel tanrTest\n";
            Console.WriteLine("##################\nTest MathLib.sin(double a)");
            if (!sinTest())
                notPassed += "Neprošel sinTest\n";
            Console.WriteLine("##################\nTest MathLib.sinr(double a)");
            if (!sinrTest())
                notPassed += "Neprošel sinrTest\n";
            Console.WriteLine("##################\nTest MathLib.cos(double a)");
            if (!cosTest())
                notPassed += "Neprošel cosTest\n";
            Console.WriteLine("##################\nTest MathLib.cosr(double a)");
            if (!cosrTest())
                notPassed += "Neprošel cosrTest\n";
            Console.WriteLine("##################\nTest MathLib.sqrt(double a)");
            if (!sqrtTest())
                notPassed += "Neprošel sqrtTest\n";
            Console.WriteLine("##################\nTest MathLib.pow(double a)");
            if (!powTest())
                notPassed += "Neprošel powTest\n";
            Console.WriteLine("##################\nTest MathLib.min(double a, double b)");
            if (!minTest())
                notPassed += "Neprošel minTest\n";
            Console.WriteLine("##################\nTest MathLib.max(double a, double b)");
            if (!maxTest())
                notPassed += "Neprošel maxTest";
            Console.ForegroundColor = ConsoleColor.Red;
            Console.WriteLine(notPassed);
            if (notPassed == string.Empty)
            {
                Console.ForegroundColor = ConsoleColor.Green;
                Console.WriteLine("Všechny testy proběhly úspěšně.");
            }
            Console.ForegroundColor = ConsoleColor.White;
        }

        public static bool factorialTest()
        {
            int total = 6;
            int passed = 0;
            
            double input = 5;
            double expectedOutput = 120;
            passed = Assert.AreEqual(expectedOutput, MathLib.fact(input), "fact(5)") ? ++passed : passed;
            input = -1;
            expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.fact(input), "fact(-1)") ? ++passed : passed;
            input = 20;
            expectedOutput = 2432902008176640000;
            passed = Assert.AreEqual(expectedOutput, MathLib.fact(input), "fact(20)") ? ++passed : passed;
            input = 1;
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.fact(input), "fact(1)") ? ++passed : passed;
            input = 5.46d;
            expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.fact(input), "fact(5.46d)") ? ++passed : passed;
            input = 0;
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.fact(input), "fact(0)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}",passed,total);
            if (passed == total)
                return true;
            return false;
        }
               
        public static bool expTest()
        {
            int total = 5;
            int passed = 0;

            double input = 0;
            double expectedOutput = 1;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.exp(input), minimalAccuracy, "exp(0)") ? ++passed : passed;
            input = 1;
            expectedOutput = 2.7182818284590452353602874713526624977572d;
            passed = Assert.AreEqual(expectedOutput, MathLib.exp(input), minimalAccuracy, "exp(1)") ? ++passed : passed;
            input = -1;
            expectedOutput = 0.367879441171442321595523770161550d;
            passed = Assert.AreEqual(expectedOutput, MathLib.exp(input), minimalAccuracy, "exp(-1)") ? ++passed : passed;
            input = expectedOutput = double.PositiveInfinity;
            passed = Assert.AreEqual(expectedOutput, MathLib.exp(input),  "exp(double.positiveInfinity)") ? ++passed : passed;
            input = 5;
            expectedOutput = Math.Pow(Math.E, 5);
            passed = Assert.AreEqual(expectedOutput, MathLib.exp(input), minimalAccuracy, "exp(5)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool declogTest()
        {
            int total = 7;
            int passed = 0;

            double input = 2;
            double expectedOutput = 0.301029995663981195213738d;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.declog(input), minimalAccuracy, "declog(2)") ? ++passed : passed;
            input = 1;
            expectedOutput = 0;
            passed = Assert.AreEqual(expectedOutput, MathLib.declog(input), minimalAccuracy, "declog(1)") ? ++passed : passed;
            input = 0;
            expectedOutput = double.NegativeInfinity;
            passed = Assert.AreEqual(expectedOutput, MathLib.declog(input), "declog(0)") ? ++passed : passed;
            input = -1;
            expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.declog(input), "declog(-1)") ? ++passed : passed;
            input = 10;
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.declog(input), minimalAccuracy, "declog(10)") ? ++passed : passed;
            input = expectedOutput = double.PositiveInfinity;
            passed = Assert.AreEqual(expectedOutput, MathLib.declog(input), "declog(double.PositiveInfinity)") ? ++passed : passed;
            input = 2.2;
            expectedOutput = 0.3424226808222062359639;
            passed = Assert.AreEqual(expectedOutput, MathLib.declog(input), minimalAccuracy, "declog(2.2)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }
         
        public static bool lnTest()
        {
            int total = 5;
            int passed = 0;

            double input = 10;
            double expectedOutput = 2.302585092994045684017991454d;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.ln(input), minimalAccuracy, "ln(10)") ? ++passed : passed;
            input = 1;
            expectedOutput = 0;
            passed = Assert.AreEqual(expectedOutput, MathLib.ln(input), minimalAccuracy, "ln(1)") ? ++passed : passed;
            input = 0;
            expectedOutput = double.NegativeInfinity;
            passed = Assert.AreEqual(expectedOutput, MathLib.ln(input), "ln(0)") ? ++passed : passed;
            input = -1;
            expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.ln(input),  "ln(-1)") ? ++passed : passed;
            input = 2.2;
            expectedOutput = 0.78845736036427016946118d;
            passed = Assert.AreEqual(expectedOutput, MathLib.ln(input), minimalAccuracy, "ln(2.2)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }
         
        public static bool logTest()
        {
            int total = 6;
            int passed = 0;

            double input = 2.2;
            double inputBase = 10;
            double expectedOutput = 0.3424226808222062359639d;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.log(input, inputBase), minimalAccuracy, "log(2.2, 10)") ? ++passed : passed;
            input = 2.2;
            inputBase = Math.E;
            expectedOutput = 0.78845736036427016946118d;
            passed = Assert.AreEqual(expectedOutput, MathLib.log(input, inputBase), minimalAccuracy, "log (2.2, Math.E)") ? ++passed : passed;
            inputBase = 0;
            expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.log(input, inputBase), "log(2.2, 0)") ? ++passed : passed;
            inputBase = 1;
            expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.log(input, inputBase), "log(2.2, 1)") ? ++passed : passed;
            inputBase = -2;
            expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.log(input, inputBase), "log(2.2, -2)") ? ++passed : passed;
            input = 0;
            inputBase = 2;
            expectedOutput = 0;
            passed = Assert.AreEqual(expectedOutput, MathLib.log(input, inputBase), "log(0, 2)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool absTest()
        {
            int total = 8;
            int passed = 0;

            double input = 0;
            double expectedOutput = 0;
            passed = Assert.AreEqual(expectedOutput, MathLib.abs(input), "abs (0)") ? ++passed : passed;
            input = -1;
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.abs(input), "abs (-1)") ? ++passed : passed;
            input = 1;
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.abs(input), "abs (1)") ? ++passed : passed;
            input = double.MaxValue;
            expectedOutput = double.MaxValue;
            passed = Assert.AreEqual(expectedOutput, MathLib.abs(input), "abs (double.MaxValue)") ? ++passed : passed;
            input = double.MinValue;
            expectedOutput = double.MaxValue;
            passed = Assert.AreEqual(expectedOutput, MathLib.abs(input), "abs (double.MinValue)") ? ++passed : passed;
            input = double.NegativeInfinity;
            expectedOutput = double.PositiveInfinity;
            passed = Assert.AreEqual(expectedOutput, MathLib.abs(input), "abs (double.NegativeInfinity)") ? ++passed : passed;
            input = double.PositiveInfinity;
            expectedOutput = double.PositiveInfinity;
            passed = Assert.AreEqual(expectedOutput, MathLib.abs(input), "abs (double.PositiveInfinity)") ? ++passed : passed;
            input = expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.abs(input), "abs (double.NaN)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool cotgTest()
        {
            int total = 9;
            int passed = 0;

            double input = 0;
            double expectedOutput = double.NaN;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotg(input), "cotg(0)") ? ++passed : passed;
            input = 90;
            expectedOutput = 0;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotg(input), minimalAccuracy, "cotg(90)") ? ++passed : passed;
            input = 180;
            expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotg(input), "cotg(180)") ? ++passed : passed;
            input = 45;
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotg(input), minimalAccuracy, "cotg(45)") ? ++passed : passed;
            input = 26.5d;
            expectedOutput = 2.00568970825901981850504087d;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotg(input), minimalAccuracy, "cotg(26.5)") ? ++passed : passed;
            input = -26.5d;
            expectedOutput = -expectedOutput;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotg(input), minimalAccuracy, "cotg(-26.5)") ? ++passed : passed;
            input = Math.PI / 2;
            expectedOutput = 0; 
            passed = Assert.AreEqual(expectedOutput, MathLib.cotg(input), minimalAccuracy, "cotg(Math.PI/2)") ? ++passed : passed;
            input = (Math.PI / 4);
            expectedOutput = 1; 
            passed = Assert.AreEqual(expectedOutput, MathLib.cotg(input), minimalAccuracy, "cotg(Math.PI/4)") ? ++passed : passed;
            input = expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotg(input), minimalAccuracy, "cotg(double.NaN)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool tanTest()
        {
            int total = 9;
            int passed = 0;

            double input = 0;
            double expectedOutput = 0;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.tan(input), minimalAccuracy, "tan(0)") ? ++passed : passed;
            input = (45);
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.tan(input), minimalAccuracy, "tan(45)") ? ++passed : passed;
            input = 90;
            expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.tan(input), minimalAccuracy, "tan(90)") ? ++passed : passed;
            input = 180;
            expectedOutput = 0;
            passed = Assert.AreEqual(expectedOutput, MathLib.tan(input), minimalAccuracy, "tan(180)") ? ++passed : passed;
            input = 405;
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.tan(input), minimalAccuracy, "tan(405)") ? ++passed : passed;
            input = 30;
            expectedOutput = (Math.Sqrt(3) / (double)3);
            passed = Assert.AreEqual(expectedOutput, MathLib.tan(input), minimalAccuracy, "tan(30)") ? ++passed : passed;
            input = 26.5;
            expectedOutput = 0.498581608053431504357127d;
            passed = Assert.AreEqual(expectedOutput, MathLib.tan(input), minimalAccuracy, "tan(26.5)") ? ++passed : passed;
            input = -input;
            expectedOutput = -expectedOutput;
            passed = Assert.AreEqual(expectedOutput, MathLib.tan(input), minimalAccuracy, "tan(-26.5)") ? ++passed : passed;
            input = expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.tan(input), minimalAccuracy, "tan(double.NaN)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool cotgrTest()
        {
            int total = 7;
            int passed = 0;

            double input = 0;
            double expectedOutput = double.NaN;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotgr(input), "cotgr(0)") ? ++passed : passed;
            input = Math.PI/2;
            expectedOutput = 0;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotgr(input), minimalAccuracy, "cotgr(Math.PI/2)") ? ++passed : passed;
            input = Math.PI;
            expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotgr(input), "cotgr(Math.PI)") ? ++passed : passed;
            input = Math.PI/4;
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotgr(input), minimalAccuracy, "cotgr(Math.PI/4)") ? ++passed : passed;
            input = Math.PI / 6;
            expectedOutput = Math.Sqrt(27) / 3;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotgr(input), minimalAccuracy, "cotgr(Math.PI/6)") ? ++passed : passed;
            input = 1;
            expectedOutput = 0.6420926159343307030064199d;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotgr(input), minimalAccuracy, "cotgr(1)") ? ++passed : passed;
            input = expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.cotgr(input), minimalAccuracy, "cotgr(double.NaN)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool sinTest()
        {
            int total = 9;
            int passed = 0;

            double input = 0;
            double expectedOutput = 0;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.sin(input), minimalAccuracy, "sin(0)") ? ++passed : passed;
            input = 180;
            passed = Assert.AreEqual(expectedOutput, MathLib.sin(input), minimalAccuracy, "sin(180)") ? ++passed : passed;
            input = 360;
            passed = Assert.AreEqual(expectedOutput, MathLib.sin(input), minimalAccuracy, "sin(360)") ? ++passed : passed;
            input = 90;
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.sin(input), minimalAccuracy, "sin(90)") ? ++passed : passed;
            input = -input;
            expectedOutput = -expectedOutput;
            passed = Assert.AreEqual(expectedOutput, MathLib.sin(input), minimalAccuracy, "sin(-90)") ? ++passed : passed;
            input = 270;
            passed = Assert.AreEqual(expectedOutput, MathLib.sin(input), minimalAccuracy, "sin(270)") ? ++passed : passed;
            input = 26.5;
            expectedOutput = 0.44619781310980879799712d;
            passed = Assert.AreEqual(expectedOutput, MathLib.sin(input), minimalAccuracy, "sin(26.5)") ? ++passed : passed;
            input = -input;
            expectedOutput = -expectedOutput;
            passed = Assert.AreEqual(expectedOutput, MathLib.sin(input), minimalAccuracy, "sin(-26.5)") ? ++passed : passed;
            input = expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.sin(input), minimalAccuracy, "sin(double.NaN)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool tanrTest()
        {
            int total = 9;
            int passed = 0;

            double input = 0;
            double expectedOutput = 0;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.tanr(input), minimalAccuracy, "tanr(0)") ? ++passed : passed;
            input = (Math.PI/4);
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.tanr(input), minimalAccuracy, "tanr(Math.PI/4)") ? ++passed : passed;
            input = Math.PI/2;
            expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.tanr(input), minimalAccuracy, "tanr(Math.PI/2)") ? ++passed : passed;
            input = Math.PI;
            expectedOutput = 0;
            passed = Assert.AreEqual(expectedOutput, MathLib.tanr(input), minimalAccuracy, "tanr(Math.PI)") ? ++passed : passed;
            input = 5*(Math.PI/4);
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.tanr(input), minimalAccuracy, "tanr(5*(Math.PI/4))") ? ++passed : passed;
            input = Math.PI/6;
            expectedOutput = (Math.Sqrt(3) / (double)3);
            passed = Assert.AreEqual(expectedOutput, MathLib.tanr(input), minimalAccuracy, "tanr(Math.PI/6)") ? ++passed : passed;
            input = 26.5;
            expectedOutput = 0.9793576431039170920061d;
            passed = Assert.AreEqual(expectedOutput, MathLib.tanr(input), minimalAccuracy, "tanr(26.5)") ? ++passed : passed;
            input = -input;
            expectedOutput = -expectedOutput;
            passed = Assert.AreEqual(expectedOutput, MathLib.tanr(input), minimalAccuracy, "tanr(-26.5)") ? ++passed : passed;
            input = expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.tanr(input), minimalAccuracy, "tanr(double.NaN)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool sinrTest()
        {
            int total = 9;
            int passed = 0;

            double input = 0;
            double expectedOutput = 0;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.sinr(input), minimalAccuracy, "sinr(0)") ? ++passed : passed;
            input = Math.PI;
            passed = Assert.AreEqual(expectedOutput, MathLib.sinr(input), minimalAccuracy, "sinr(Math.PI)") ? ++passed : passed;
            input = 2*Math.PI;
            passed = Assert.AreEqual(expectedOutput, MathLib.sinr(input), minimalAccuracy, "sinr(2*Math.PI)") ? ++passed : passed;
            input = Math.PI/2;
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.sinr(input), minimalAccuracy, "sinr(Math.PI/2)") ? ++passed : passed;
            input = -input;
            expectedOutput = -expectedOutput;
            passed = Assert.AreEqual(expectedOutput, MathLib.sinr(input), minimalAccuracy, "sinr(-Math.PI/2)") ? ++passed : passed;
            input = 3*Math.PI/2;
            passed = Assert.AreEqual(expectedOutput, MathLib.sinr(input), minimalAccuracy, "sinr(3*Math.PI/2)") ? ++passed : passed;
            input = 26.5;
            expectedOutput = 0.979357643103917092006d;
            passed = Assert.AreEqual(expectedOutput, MathLib.sinr(input), minimalAccuracy, "sinr(26.5)") ? ++passed : passed;
            input = -input;
            expectedOutput = -expectedOutput;
            passed = Assert.AreEqual(expectedOutput, MathLib.sinr(input), minimalAccuracy, "sinr(-26.5)") ? ++passed : passed;
            input = expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.sinr(input), minimalAccuracy, "sinr(double.NaN)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool cosTest()
        {
            int total = 9;
            int passed = 0;

            double input = 0;
            double expectedOutput = 1;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.cos(input), minimalAccuracy, "cos(0)") ? ++passed : passed;
            input = 360;
            passed = Assert.AreEqual(expectedOutput, MathLib.cos(input), minimalAccuracy, "cos(360)") ? ++passed : passed;
            input = 180;
            expectedOutput = -expectedOutput;
            passed = Assert.AreEqual(expectedOutput, MathLib.cos(input), minimalAccuracy, "cos(180)") ? ++passed : passed;
            input = 90;
            expectedOutput = 0;
            passed = Assert.AreEqual(expectedOutput, MathLib.cos(input), minimalAccuracy, "cos(90)") ? ++passed : passed;
            input = -input;
            passed = Assert.AreEqual(expectedOutput, MathLib.cos(input), minimalAccuracy, "cos(-90)") ? ++passed : passed;
            input = 270;
            passed = Assert.AreEqual(expectedOutput, MathLib.cos(input), minimalAccuracy, "cos(270)") ? ++passed : passed;
            input = 26.5;
            expectedOutput = 0.89493436160202505655d;
            passed = Assert.AreEqual(expectedOutput, MathLib.cos(input), minimalAccuracy, "cos(26.5)") ? ++passed : passed;
            input = -input;
            expectedOutput = -expectedOutput;
            passed = Assert.AreEqual(expectedOutput, MathLib.cos(input), minimalAccuracy, "cos(-26.5)") ? ++passed : passed;
            input = expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.cos(input), minimalAccuracy, "cos(double.NaN)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool cosrTest()
        {
            int total = 9;
            int passed = 0;

            double input = 0;
            double expectedOutput = 1;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.cosr(input), minimalAccuracy, "cosr(0)") ? ++passed : passed;
            input = 2 * Math.PI;
            passed = Assert.AreEqual(expectedOutput, MathLib.cosr(input), minimalAccuracy, "cosr(2 * Math.PI)") ? ++passed : passed;
            input = Math.PI;
            expectedOutput = -expectedOutput;
            passed = Assert.AreEqual(expectedOutput, MathLib.cosr(input), minimalAccuracy, "cosr(Math.PI)") ? ++passed : passed;
            input = Math.PI/2;
            expectedOutput = 0;
            passed = Assert.AreEqual(expectedOutput, MathLib.cosr(input), minimalAccuracy, "cosr(Math.PI/2)") ? ++passed : passed;
            input = -input;
            passed = Assert.AreEqual(expectedOutput, MathLib.cosr(input), minimalAccuracy, "cosr(-Math.PI/2)") ? ++passed : passed;
            input = 3*Math.PI/2;
            passed = Assert.AreEqual(expectedOutput, MathLib.cosr(input), minimalAccuracy, "cosr(3*Math.PI/2)") ? ++passed : passed;
            input = 26.5;
            expectedOutput = 0.20213512038718198561988d;
            passed = Assert.AreEqual(expectedOutput, MathLib.cosr(input), minimalAccuracy, "cosr(26.5)") ? ++passed : passed;
            input = -input;
            expectedOutput = 0.20213512038718198561988374015165720987d;
            passed = Assert.AreEqual(expectedOutput, MathLib.cosr(input), minimalAccuracy, "cosr(-26.5)") ? ++passed : passed;
            input = expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.cosr(input), minimalAccuracy, "cosr(double.NaN)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool sqrtTest()
        {
            int total = 5;
            int passed = 0;

            double input = 0;
            double expectedOutput = 0;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.sqrt(input), minimalAccuracy, "sqrt(0)") ? ++passed : passed;
            input = 2;
            expectedOutput = 1.414213562373095048801688724d;
            passed = Assert.AreEqual(expectedOutput, MathLib.sqrt(input), minimalAccuracy, "sqrt(2)") ? ++passed : passed;
            input = expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.sqrt(input), minimalAccuracy, "sqrt(1)") ? ++passed : passed;
            input = 1000000;
            expectedOutput = 1000;
            passed = Assert.AreEqual(expectedOutput, MathLib.sqrt(input), minimalAccuracy, "sqrt(1000000)") ? ++passed : passed;
            input = -1;
            expectedOutput = double.NaN;
            passed = Assert.AreEqual(expectedOutput, MathLib.sqrt(input), minimalAccuracy, "sqrt(-1)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool powTest()
        {
            int total = 6;
            int passed = 0;

            double input = 0;
            double input2 = 3;
            double expectedOutput = 0;
            double minimalAccuracy = 1e-15;
            passed = Assert.AreEqual(expectedOutput, MathLib.pow(input,input2), minimalAccuracy, "pow(0,3)") ? ++passed : passed;
            input = 3;
            input2 = 0;
            expectedOutput = 1;
            passed = Assert.AreEqual(expectedOutput, MathLib.pow(input, input2), minimalAccuracy, "pow(3,0)") ? ++passed : passed;
            input = -2;
            input2 = 2;
            expectedOutput = 4;
            passed = Assert.AreEqual(expectedOutput, MathLib.pow(input, input2), minimalAccuracy, "pow(-2,2)") ? ++passed : passed;
            input = -2;
            input2 = 3;
            expectedOutput = 8;
            passed = Assert.AreEqual(expectedOutput, MathLib.pow(input, input2), minimalAccuracy, "pow(-2,3)") ? ++passed : passed;
            input = 2;
            input2 = -1;
            expectedOutput = 0.5d;
            passed = Assert.AreEqual(expectedOutput, MathLib.pow(input, input2), minimalAccuracy, "pow(2,-1)") ? ++passed : passed;
            input2 = -2;
            expectedOutput = .25d;
            passed = Assert.AreEqual(expectedOutput, MathLib.pow(input, input2), minimalAccuracy, "pow(2,-2)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool minTest()
        {
            int total = 5;
            int passed = 0;

            double input = 0;
            double input2 = 0;
            double expectedOutput = 0;
            passed = Assert.AreEqual(expectedOutput, MathLib.min(input,input2), "min(0,0)") ? ++passed : passed;
            input = double.NegativeInfinity;
            input2 = double.MinValue;
            expectedOutput = double.NegativeInfinity;
            passed = Assert.AreEqual(expectedOutput, MathLib.min(input, input2), "min(double.NegativeInfinity,double.MinValue)") ? ++passed : passed;
            input = double.MaxValue;
            input2 = double.PositiveInfinity;
            expectedOutput = double.MaxValue;
            passed = Assert.AreEqual(expectedOutput, MathLib.min(input, input2), "min(double.PositiveInfinity,double.MaxValue)") ? ++passed : passed;
            input = 1;
            input2 = 2;
            expectedOutput = input;
            passed = Assert.AreEqual(expectedOutput, MathLib.min(input, input2), "min(1,2)") ? ++passed : passed;
            input = 2;
            input2 = 1;
            expectedOutput = input2;
            passed = Assert.AreEqual(expectedOutput, MathLib.min(input, input2), "min(2,1)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }

        public static bool maxTest()
        {
            int total = 5;
            int passed = 0;

            double input = 0;
            double input2 = 0;
            double expectedOutput = 0;
            passed = Assert.AreEqual(expectedOutput, MathLib.max(input, input2), "max(0,0)") ? ++passed : passed;
            input = double.NegativeInfinity;
            input2 = double.MinValue;
            expectedOutput = double.MinValue;
            passed = Assert.AreEqual(expectedOutput, MathLib.max(input, input2), "max(double.NegativeInfinity,double.MinValue)") ? ++passed : passed;
            input = double.MaxValue;
            input2 = double.PositiveInfinity;
            expectedOutput = double.PositiveInfinity;
            passed = Assert.AreEqual(expectedOutput, MathLib.max(input, input2), "max(double.PositiveInfinity,double.MaxValue)") ? ++passed : passed;
            input = 1;
            input2 = 2;
            expectedOutput = input2;
            passed = Assert.AreEqual(expectedOutput, MathLib.max(input, input2), "max(1,2)") ? ++passed : passed;
            input = 2;
            input2 = 1;
            expectedOutput = input;
            passed = Assert.AreEqual(expectedOutput, MathLib.max(input, input2), "max(2,1)") ? ++passed : passed;
            Console.WriteLine("Passed {0}/{1}", passed, total);
            if (passed == total)
                return true;
            return false;
        }
    }//class
}//namespace
