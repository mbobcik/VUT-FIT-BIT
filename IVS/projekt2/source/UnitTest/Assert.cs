using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace UnitTestConsole
{
    class Assert
    {
        public static bool AreEqual(double expectedOutput, double input, double accuracy, string message)
        {
            if((Math.Abs(expectedOutput-input) <= accuracy) || ((double.IsNaN(expectedOutput)) && double.IsNaN(input)))
            {
                Console.WriteLine("\tAssert.AreEqual Passed.   Expected: <{0}> Actual: <{1}>. Message: {2}",expectedOutput, input,message);
                return true;
            }
            else
            {
                Console.WriteLine("\tAssert.AreEqual *FAILED*. Expected  <{0} +- {1}> Actual <{2}>. Message:{3}",expectedOutput, accuracy, input,message);
                return false;
            }
        }

        public static bool AreEqual(int expectedOutput, int input, string message)
        {
            if (expectedOutput == input)
            {
                Console.WriteLine("\tAssert.AreEqual Passed.   Expected: <{0}> Actual: <{1}>. Message: {2}",expectedOutput, input, message);

                return true;
            }
            else
            {
                Console.WriteLine("\tAssert.AreEqual *FAILED*. Expected: <{0}> Actual: <{1}>. Message: {2}", expectedOutput, input, message);
                return false;
            }
        }

        public static bool AreEqual(double expectedOutput, double input, string message)
        {
            if ((expectedOutput == input) || ((double.IsNaN(expectedOutput)) && double.IsNaN(input)))
            {
                Console.WriteLine("\tAssert.AreEqual Passed.   Expected: <{0}> Actual: <{1}>. Message: {2}",expectedOutput, input, message);

                return true;
            }
            else
            {
                Console.WriteLine("\tAssert.AreEqual *FAILED*. Expected: <{0}> Actual: <{1}>. Message: {2}", expectedOutput, input, message);
                return false;
            }
        }


    }
}
