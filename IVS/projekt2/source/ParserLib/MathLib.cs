using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Linq;
using System.Text;
using System.Threading.Tasks;


namespace ParserLib
{
    [System.AttributeUsage(System.AttributeTargets.Method |
                       System.AttributeTargets.Struct,
                       AllowMultiple = true)]
    public class Popis : System.Attribute
    {
        public string name { get; set; }

        public Popis(string name)
        {
            this.name = name;
        }

        public string GetName()
        {
            return name;
        }
    }

    public static class MathLib
    {

        private static Random r;
        /// <summary>
        /// Vrací true, pokud je daný výraz číselného charakteru
        /// </summary>
        /// <param name="Expression"></param>
        /// <returns>Vrací true pokud je výraz číselný</returns>
        static MathLib()
        {
            r = new Random();
        }
        public static bool IsNumeric(object Expression)
        {
            double retNum;
            return Double.TryParse(Convert.ToString(Expression), System.Globalization.NumberStyles.Any, System.Globalization.NumberFormatInfo.InvariantInfo, out retNum);
        }

        /// <summary>
        /// Vracia väčšie z dvoch čísel
        /// </summary>
        /// <param name="a">Prvé číslo, ktoré sa bude porovnávať</param>
        /// <param name="b">Druhé číslo, ktoré sa bude porovnávať</param>
        /// <returns>Vracia väčšie z dvoch zadaných čísel a,b</returns>
        [Popis("Vrací větší ze dvou čísel")]
        public static double max(double a, double b)
        {
            return Math.Max(a, b);

        }

        /// <summary>
        /// Vracia menšie z dvoch čísel
        /// </summary>
        /// <param name="a">Prvé číslo, ktoré sa bude porovnávať</param>
        /// <param name="b">Druhé číslo, ktoré sa bude porovnávať</param>
        /// <returns>Vracia menšie z dvoch zadaných čísel a,b</returns>
        [Popis("Vrací menší ze dvou čísel")]
        public static double min(double a, double b)
        {
            return Math.Min(a, b);
        }

        /// <summary>
        /// Vracia prvé číslo umocnené na druhé
        /// </summary>
        /// <param name="a">Základ - číslo, ktoré bude umocňované</param>
        /// <param name="b">Exponent - číslo, na ktoré bude umocnený základ</param>
        /// <returns></returns>
        [Popis("Vrací první číslo umocněné na druhé")]
        public static double pow(double a, double b)
        {
            return Math.Pow(a, b);
        }

        /// <summary>
        /// Vracia náhodné číslo z intervalu
        /// </summary>
        /// <param name="min">Minimum intervalu, z ktorého bude generované čislo</param>
        /// <param name="max">Maximum intervalu, z ktorého bude generované čislo</param>
        /// <returns>Vracia náhodné celé číslo z intervalu <a;b></returns>
        [Popis("Vrací náhodné celé číslo v intervalu <a;b>")]
        public static double rnd(double min, double max)
        {
            return r.Next(int.Parse(min.ToString()), int.Parse(max.ToString()));
        }
        /// <summary>
        /// Vrací odmocninu čísla
        /// </summary>
        /// <param name="a">Číslo, ktorého odmocnina bude spočítaná</param>
        /// <returns>Vracia odmocninu čísla a</returns>
        [Popis("Vrací druhou odmocninu čísla")]
        public static double sqrt(double a)
        {
            return Math.Sqrt(a);
        }

        /// <summary>
        /// Vrací b-tou odmocninu čísla a
        /// </summary>
        /// <param name="a">Číslo, ktorého odmocnina bude spočítaná</param>
        /// <param name="b">Číslo vyjadřující kolikátá odmocnina bude spočítána</param>
        /// <returns>Vrací b-tou odmocninu čísla a</returns>
        [Popis("Vrací b-tou odmocninu čísla a")]
        public static double sqrtn(double a, double b)
        {
            return Math.Pow(a, 1 / b); 
        }
        /// <summary>
        /// Vracia faktoriál čísla
        /// </summary>
        /// <param name="a">Číslo, ktorého faktoriál bude spočítaný</param>
        /// <returns>Vracia faktoriál čísla a</returns>
        [Popis("Vrací faktoriál čísla")]
        public static double fact(double a)
        {
            if (a < 0)
                return double.NaN;
            if (a == 0)
                return 1;
            else
                return (a * fact(a - 1));
        }


        /// <summary>
        /// Vráti sinus čísla, ktoré je v raiánoch
        /// </summary>
        /// <param name="x">Číslo v radiánoch, ktorého sinus bude spočítaný</param>
        /// <returns>Vracia sinus čísla x</returns>
        [Popis("Vrací sinus čísla (vstup v radiánech)")]
        public static double sinr(double x)
        {
            double dva_pi = 2 * Math.PI;
            if (x > 0)
            {
                while (x > dva_pi)     // sinus ma periodu 2PI
                {
                    x = x - dva_pi;
                }
            }
            if (x < 0)
            {
                while (x < -dva_pi)
                {
                    x = x + dva_pi;
                }
            }

            long n = 2;
            double t, vys = x, vys_p = 0;
            double EPSILON = 10e-20;
            double t_p = x;

            do
            {
                t = t_p * ((-x * x) / (n * (n + 1)));   // čeln polynomu - počíta sa z predchadzajúceho členu
                vys_p = vys;
                t_p = t;
                vys += t;
                n += 2;
            } while (Math.Abs(vys - vys_p) > EPSILON);

            return Math.Round(vys, 15);
        }

        /// <summary>
        /// Vracia cosinus čisla ktore je v radiánoch
        /// </summary>
        /// <param name="x">Číslo v radiánoch, ktorého cosinus bude spočítaný</param>
        /// <returns>Vracia cosinus čísla x</returns>
        [Popis("Vrací cosinus čísla (vstup v radiánech)")]
        public static double cosr(double x)
        {
            double dva_pi = 2 * Math.PI;
            if (x > 0)
            {
                while (x > dva_pi)     // cosinus ma periodu 2PI
                {
                    x = x - dva_pi;
                }
            }
            if (x < 0)
            {
                while (x < -dva_pi)
                {
                    x = x + dva_pi;
                }
            }

            long n = 1;
            double t, vys = 1, vys_p = 0;
            double EPSILON = 10e-20;
            double t_p = 1;

            do
            {
                t = t_p * ((-x * x) / (n * (n + 1)));
                vys_p = vys;
                t_p = t;
                vys += t;
                n += 2;
            } while (Math.Abs(vys - vys_p) > EPSILON);

            return Math.Round(vys, 15);
        }

        /// <summary>
        /// Vrací tangens daného čísla, které je v RADIÁNECH
        /// </summary>
        /// <param name="a">Číslo v radiánoch, ktorého tangens bude spočítaný</param>
        /// <returns>Vracia tangens čísla a</returns>
        [Popis("Vrací tangens čísla (vstup v radiánech)")]
        public static double tanr(double a)
        {
            double cosinus = cosr(a);
            if (cosinus == 0)
                return double.NaN;
            return sinr(a) / cosinus;
        }
        /// <summary>
        /// Vrací tangens daného čísla, které je v RADIÁNECH
        /// </summary>
        /// <param name="a">Číslo v radiánoch, ktorého tangens bude spočítaný</param>
        /// <returns>Vracia tangens čísla a</returns>
        [Popis("Vrací tangens čísla (vstup v radiánech)")]
        public static double tgr(double a)
        {
            return tanr(a);
        }
        /// <summary>
        /// Vrací tangens daného čísla, které je v RADIÁNECH
        /// </summary>
        /// <param name="a">Číslo v radiánoch, ktorého tangens bude spočítaný</param>
        /// <returns>Vracia tangens čísla a</returns>
        [Popis("Vrací tangens čísla (vstup v radiánech)")]
        public static double tangr(double a)
        {
            return tanr(a);
        }

        /// <summary>
        /// Vracia cotangens čísla, ktoré je v RADIÁNOCH
        /// </summary>
        /// <param name="a">Číslo v radiánoch, ktorého cotangens bude spočítaný</param>
        /// <returns>Vracia cotangens čísla a</returns>
        [Popis("Vrací cotangens čísla (vstup v radiánech)")]
        public static double cotgr(double a)
        {
            double sinus = sinr(a);
            if (sinus == 0)
                return double.NaN;
            return cosr(a) / sinus;
        }

        /// <summary>
        /// Vracia cotangens čísla, ktoré je v RADIÁNOCH
        /// </summary>
        /// <param name="a">Číslo v radiánoch, ktorého cotangens bude spočítaný</param>
        /// <returns>Vracia cotangens čísla a</returns>
        [Popis("Vrací cotangens čísla (vstup v radiánech)")]
        public static double cotanr(double a)
        {
            return cotgr(a);
        }

        /// <summary>
        /// Prepočíta veľkosť uhla zadaného v stupňoch do radiánov
        /// </summary>
        /// <param name="a">Veľkosť uhla v stupňoch, ktorá sa prepočíta na radiány</param>
        /// <returns>Vracia uhol v radiánoch odpovedajúci zadanému uhlu v stupňoch</returns>
        private static double DegreeToRadian(double a)
        {
            while (a > 360)     // perióda gon. funkcii sínus a kosínus
            {
                a -= 360;
            }
            return Math.PI * a / 180.0;
        }

        /// <summary>
        /// Vrací sinus daného čísla, ktoré je v STUPŇOCH
        /// </summary>
        /// <param name="a">Číslo v stupňoch, ktorého sinus bude spočítaný</param>
        /// <returns>Vracia sinus čisla a</returns>
        [Popis("Vrací sinus čísla (vstup ve stupních)")]
        public static double sin(double a)
        {
            return sinr(DegreeToRadian(a));
        }

        /// <summary>
        /// Vracia cosinus dného čísla, ktoré je v STUPŇOCH
        /// </summary>
        /// <param name="a">Číslo v stupňoch, ktorého cosinus bude spočítaný</param>
        /// <returns>Vracia cosinus čisla a</returns>
        [Popis("Vrací cosinus čísla (vstup ve stupních)")]
        public static double cos(double a)
        {
            return cosr(DegreeToRadian(a));
        }

        /// <summary>
        /// Vracia tangens daného čísla, ktoré je v STUPŇOCH
        /// </summary>
        /// <param name="a">Číslo v stupňoch, ktorého tangens bude spočítaný</param>
        /// <returns>Vracia tangens čisla a</returns>
        [Popis("Vrací tangens čísla (vstup ve stupních)")]
        public static double tan(double a)
        {
            return tanr(DegreeToRadian(a));
        }

        /// <summary>
        /// Vracia tangens daného čísla, ktoré je v STUPŇOCH
        /// </summary>
        /// <param name="a">Číslo v stupňoch, ktorého tangens bude spočítaný</param>
        /// <returns>Vracia tangens čisla a</returns>
        [Popis("Vrací tangens čísla (vstup ve stupních)")]
        public static double tg(double a)
        {
            return tanr(DegreeToRadian(a));
        }

        /// <summary>
        /// Vracia tangens daného čísla, ktoré je v STUPŇOCH
        /// </summary>
        /// <param name="a">Číslo v stupňoch, ktorého tangens bude spočítaný</param>
        /// <returns>Vracia tangens čisla a</returns>
        [Popis("Vrací tangens čísla (vstup ve stupních)")]
        public static double tang(double a)
        {
            return tanr(DegreeToRadian(a));
        }

        /// <summary>
        /// Vracia cotangens daného čísla, ktoré je v STUPŇOCH
        /// </summary>
        /// <param name="a">Číslo v stupňoch, ktorého cotangens bude spočítaný</param>
        /// <returns>Vracia tangens čisla a</returns>
        [Popis("Vrací cotangens čísla (vstup ve stupních)")]
        public static double cotan(double a)
        {
            return cotgr(DegreeToRadian(a));
        }

        /// <summary>
        /// Vracia cotangens daného čísla, ktoré je v STUPŇOCH
        /// </summary>
        /// <param name="a">Číslo v stupňoch, ktorého cotangens bude spočítaný</param>
        /// <returns>Vracia tangens čisla a</returns>
        [Popis("Vrací cotangens čísla (vstup ve stupních)")]
        public static double cotg(double a)
        {
            return cotgr(DegreeToRadian(a));
        }

        /// <summary>
        /// Vracia absoltnú hodnotu čísla
        /// </summary>
        /// <param name="a">Číslo, ktorého absolútna hodnota bude spočítaná</param>
        /// <returns>Vracia absolútnu hodnotu čísla a</returns>
        [Popis("Vrací absolutní hodnotu čísla")]
        public static double abs(double a)
        {
            if (a < 0)
                return -a;
            else
                return a;
        }

        /// <summary>
        /// Vracia logaritmus čisla pri ľubovoľnom základe
        /// </summary>
        /// <param name="a">Číslo ktorého logaritmus má byť nájdený</param>
        /// <param name="b">Číslo špecifikujúce základ logaritmu</param> 
        /// <returns>Vracia logaritmus čísla a pri základe b</returns>
        [Popis("Log a o základu b")]
        public static double log(double a, double b)
        {
            if (a == 0)
                return 0;
            if (b <= 0 || a < 0)
                return double.NaN;
            return Math.Log(a, b);
        }

        /// <summary>
        /// Vracia prirodzený logaritmus čísla
        /// </summary>
        /// <param name="a">Číslo, ktorého prirodzený logaritmus bude spočítaný</param>
        /// <returns>Vracia prirodzený logaritmus čísla a</returns>
        [Popis("Vrací přirozený logaritmus")]
        public static double ln(double a)
        {
            if (a < 0)
                return double.NaN;
            if (a == 0)
                return double.NegativeInfinity;
            return Math.Log(a);
        }

        /// <summary>
        /// Vracia dekadický logaritmus čísla
        /// </summary>
        /// <param name="a">Číslo, ktorého dekadický logaritmus bude spočítaný</param>
        /// <returns>Vracia dekadický logaritmus čísla a</returns>
        [Popis("Vrací dekadický logaritmus")]
        public static double declog(double a)
        {
            if (a < 0)
                return double.NaN;
            if (a == 0)
                return double.NegativeInfinity;
            return Math.Log10(a);
        }

        /// <summary>
        /// Vracia hodnotu e(Eulerovo číslo) umocenú na špecifikovanu mocninu
        /// </summary>
        /// <param name="a">Číslo, na ktoré bude umocnené Eulerovo číslo</param>
        /// <returns>Vracia hodnotu čísla e(Eulerovo číslo) umocenú na špecifikovanu mocninu a</returns>
        [Popis("Vrací E umocněná na a")]
        public static double exp(double a)
        {
            return Math.Exp(a);
        }


    }
}
