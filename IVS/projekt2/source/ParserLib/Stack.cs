using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace ParserLib
{
    public class StackC : List<string>
    {
        /// <summary>
        /// Vrátí řetezec na posledním indexu a poté ji smaže
        /// </summary>
        /// <returns>Vrátí hodnotu na posledním indexu</returns>
        public string pop()
        {
            string toReturn = this[this.Count - 1];
            this.RemoveAt(this.Count - 1);

            return toReturn;
        }

        /// <summary>
        /// Přidá řetězec na vrchol zásobníku
        /// </summary>
        /// <param name="insertstr"></param>
        public void push(string insertstr)
        {
            this.Add(insertstr);
        }
    }
}
