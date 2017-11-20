using System;
using System.Collections.Generic;
using System.Linq;
using System.Reflection;
using System.Text;
using System.Threading.Tasks;

namespace ParserLib
{
    public class Parser
    {
        /// <summary>
        /// Variable stores last used function
        /// </summary>
        private string function { get; set; }
        /// <summary>
        /// A list of string containing output stack of tokens
        /// </summary>
        private List<string> output { get; set; }
        /// <summary>
        /// Stack containing tokens of operators and other non numeric characters
        /// </summary>
        private StackC stack { get; set; }



        /// <summary>
        /// Konstruktor Parseru, jsou zde deklarovány potřebné proměnné
        /// </summary>
        public Parser()
        {
            this.function = ""; //vynulování funkce
            this.stack = new StackC(); //vytvoření instance zásobníku
            output = new List<string>(); //vytvoření instance seznamu parsované funkce
        }

        /// <summary>
        /// Returns precedence of a token (+,-,*,/...) for math equations
        /// </summary>
        /// <param name="a"></param>
        /// <returns></returns>
        private int Precedence(char a)
        {
            char[] low = { '+', '-' };
            char[] big = { '*', '/' };
            char[] biggest = { '^' };

            if (low.Contains(a))
                return 0;
            else if (big.Contains(a))
                return 1;
            else if (biggest.Contains(a))
                return 2;
            else
                return 99;

        }

        /// <summary>
        /// Parsuje daný řetězec a počítá ho
        /// </summary>
        /// <param name="func">Řetězec obsahující matematický výraz</param>
        /// <returns>Vrací jednočíselný vyýsledek daného výrazu</returns>
        public double Vypocitat(string func)
        {
            this.function = trimFunc(func); //ořezání nechtěných znaků, mezer...
            if (parseFunc(function) == 1) //pokud parsování skončí s chybou
            {
                //chyba
                return double.NaN; // není číslo
            }
            try
            {
                constantsReplace(); //nahrazení konstant hodnotami
       
                    for (int i = 0; i < output.Count; i++)
                    {
                        if (!MathLib.IsNumeric(output[i]))
                        {
                            int jmp = 2;
                            Type StaticClass = Type.GetType("ParserLib.MathLib", true); // reflexe - volání metod 

                            switch (output[i].ToLower()) // rozhoování a zjištěných operacích
                            {
                                case "+":
                                    output[i - 2] = (double.Parse(output[i - 2]) + double.Parse(output[i - 1])).ToString();
                                    break;
                                case "-":
                                    output[i - 2] = (double.Parse(output[i - 2]) - double.Parse(output[i - 1])).ToString();
                                    break;
                                case "*":
                                    output[i - 2] = (double.Parse(output[i - 2]) * double.Parse(output[i - 1])).ToString();
                                    break;
                                case "/":
                                    output[i - 2] = (double.Parse(output[i - 2]) / double.Parse(output[i - 1])).ToString();
                                    break;
                                case "^":
                                    output[i - 2] = (Math.Pow(double.Parse(output[i - 2]), double.Parse(output[i - 1]))).ToString();
                                    break;
                                default:
                                    MethodInfo methodInfo = StaticClass.GetMethod(output[i].ToLower()); // získání metody z mat. knihovny
                                    object[] arguments;
                                    ParameterInfo[] parameters = methodInfo.GetParameters(); // vytvoření parametrů pro předání
                                    if (parameters.Length == 1) // pokud má metoda jen jeden parametr
                                    {
                                        jmp = 1;
                                        arguments = new object[] { double.Parse(output[i - 1]) };
                                    }
                                    else // pokud více
                                    {
                                        arguments = new object[] { double.Parse(output[i - 2]), double.Parse(output[i - 1]) };
                                    }

                                    output[i - parameters.Length] = methodInfo.Invoke(null, arguments).ToString(); // zavoláme metodu přes delegát spolu s argumenty
                                    break;

                            }
                            for (int j = i + (1 - jmp); j < output.Count - jmp; j++) // posunutí použitých prvků v poli
                            {
                                output[j] = output[j + jmp];

                            }
                            output.RemoveRange(output.Count - jmp, jmp); // odstranění využitých prvků
                            i -= jmp;
                           
                        }
                    }
                
                //Přidáme stack

                //počítáme druhou část umístěnou ve stacku
                for (int i = 0; i < stack.Count; i++)
                {
                    switch (stack[i])
                    {

                        case "+":
                            output[0] = (double.Parse(output[0]) + double.Parse(output[1])).ToString();
                            break;
                        case "-":
                            output[0] = (double.Parse(output[0]) - double.Parse(output[1])).ToString();
                            break;
                        case "*":
                            output[0] = (double.Parse(output[0]) * double.Parse(output[1])).ToString();
                            break;
                        case "/":
                            output[0] = (double.Parse(output[0]) / double.Parse(output[1])).ToString();
                            break;
                        case "^":
                            output[0] = (Math.Pow(double.Parse(output[0]), double.Parse(output[1]))).ToString();
                            break;

                    }
                    for (int j = 1; j < output.Count - 1; j++)
                    {
                        output[j] = output[j + 1];

                    }
                    output.RemoveRange(output.Count - 1, 1);
                }
                if (output.Count > 1)
                {
                    return double.NaN;
                }
                return double.Parse(output[0]);
            }
            catch (Exception ex)
            {
                return double.NaN; 
            }
           



        }

      
        /// <summary>
        /// Nahrazuje konstanty z řetězce požadovanými hodnotami
        /// </summary>
        private void constantsReplace()
        {
            for (int i = 0; i < output.Count; i++)
            {
                switch(output[i].ToLower())
                {
                    case "π":
                    case "pi":
                        output[i] = Math.PI.ToString();
                        break;
                    
                    case "e":
                        output[i] = Math.E.ToString();
                        break;
                       
                } 
            } 
        }
        /// <summary>
        /// Pomocí reflexe získává informace o metodách ze třídy MathLib
        /// </summary>
        /// <returns>Vrací pole typu MethodInfo[], obsahující informace o metodách ve třídě MathLib</returns>
        public MethodInfo[] getMathFunctions()
        {
            Type MathClass = Type.GetType("ParserLib.MathLib", true);
            return MathClass.GetMethods(BindingFlags.Static|BindingFlags.Public); // získání metody z mat. knihovny
        }
        /// <summary>
        /// Parses function into stack and output field, returns 0 id there were no errors, oterwise return 1
        /// </summary>
        /// <param name="func"></param>
        /// <returns></returns>
        private int parseFunc(string func)
        {
            string[] pole = func.Split(' ');

            try
            {
                for (int i = 0; i < pole.Length; i++) // procházíme jednotlivé znaky
                {
                    if (MathLib.IsNumeric(pole[i])) // pokud je číslo - uložíme na výstup
                    {
                        // vlozit do fronty 
                        output.Add(pole[i]);
                    }
                    else if (pole[i] == "(") // pokud je otevírací závorka, vložíme ho na zásobník
                    {

                        stack.push(pole[i]);
                    }
                    else if (pole[i] == ")") // pokud je uzavírací závorka
                    {
                        // algorhithm
                        while (stack[stack.Count - 1] != "(") // dokud ve stacku nenarazíme na otevírací závorku
                        {
                            output.Add(stack.pop().ToString()); // vyndáme ze stacku a vložíme na výstup
                        }
                        stack.pop(); // poslední vyndáme ze stacku
                    }

                    else if (pole[i] == ";") // oddělovač argumentů ve funkcích
                    {

                        while(stack[stack.Count - 1] != "(") // hledáme na zásobníku otevírací závorku
                            output.Add(stack.pop().ToString()); // vyndáme ze stacku a vložíme na výstup
                    }
                    else
                    {
                        if (stack.Count > 0) // dokud je něco na stacku
                        {

                            while (stack.Count > 0 && Precedence(stack[stack.Count - 1].ToCharArray()[0]) > Precedence(pole[i].ToCharArray()[0]) && stack[stack.Count - 1].ToCharArray()[0] != '(')
                            {
                                output.Add(stack.pop().ToString()); // převod zásobníku na výstup
                            }
                        }
                        stack.push(pole[i]);
                    }
                }
                // zbytek stacku odzadu nahrneme na výstup a poté smažeme
                for (int i = stack.Count - 1; i >= 0; i--)
                {
                    if (stack[i] != "+" && stack[i] != "-")
                    {
                        output.Add(stack[i].ToString());
                        stack.RemoveAt(i);
                    }

                }
            }
            catch (Exception ex)
            {
                return 1;
            }
            return 0;
        }
      
        /// <summary>
        /// Ořeže nechtěné znaky z řetězce
        /// </summary>
        /// <param name="func">Řetězec k ořezání</param>
        /// <returns>Ořezaný řetězec</returns>
        private string trimFunc(string func)
        {

            for (int i = 0; i < func.Length; i++)
            {
                if (func[i] == ' ' || func[i] == '\t')
                {
                    func = func.Remove(i, 1); 
                } 
            }

            string edited = String.Empty;
            try
            {
                char lastChar = '\\';
                for (int i = 0; i < func.Length; i++)
                {
                    if (lastChar == '\\' && !Char.IsLetter(func[i]))
                    {
                        edited += func[i];
                    }
                    else if (Char.IsLetter(func[i]))
                    {
                        int iteraci = 0;
                        edited += " ";
                        while (i+iteraci < func.Length && Char.IsLetter(func[i + iteraci]))
                        {
                            edited += func[i + iteraci];
                            iteraci++;
                        }

                        i += (iteraci - 1);
                    }
                    else
                    {
                        if (Char.IsDigit(func[i]) || func[i] == '.' || func[i] == ',')
                        {
                            if (Char.IsDigit(lastChar) || lastChar == '.' || lastChar == ',')
                            {
                                if (func[i] == '.' || func[i] == ',')
                                {
                                    edited += ",";
                                }
                                else
                                    edited += func[i];
                            }
                            else if (lastChar == '-' || lastChar == '+')
                            {
                                if (i - 2 < 0)
                                {
                                    edited = "( " + lastChar.ToString() + func[i] + " )"; 
                                }
                                else if (( (i > 1 && (func[i - 2] == '*' || func[i-2] == '-' || func[i - 2] == '+' || func[i - 2] == '/' || func[i - 2] == '^' || func[i - 2] == ';')) || (i == 1)) && func[i - 2] != ')')
                                    edited = edited.Substring(0, edited.Length - 2) + " ( " + lastChar.ToString()  + func[i] + " )";
                                else if(i > 1 && func[i - 2] == '(')
                                     edited += func[i];
                                else
                                    edited += " " + func[i];


                            }
                            else
                            {
                                edited += " " + func[i];
                            }
                        }



                        else if (func[i] != ' ')
                        {
                            edited += " " + func[i];
                        }


                    }

                    lastChar = func[i];
                }
                if (edited.Trim()[0] == '+' || edited.Trim()[0] == '-')
                {
                    return "0 " + edited.Trim();
                 }
                    return edited.Trim();
            }
            catch (Exception ex)
            {
                return ""; 
            }
        }
    }
}
