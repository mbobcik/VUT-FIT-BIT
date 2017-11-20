using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Reflection;
using System.Runtime.InteropServices;
using System.Text;
using System.Threading;
using System.Threading.Tasks;
using System.Windows.Forms;
using ParserLib;


namespace Calculator
{
    public partial class Form1 : Form
    {
        //Volání systémových prostředků - pohyb okna
        private const int WM_NCLBUTTONDOWN = 0xA1;
        private const int HT_CAPTION = 0x2;

        [DllImportAttribute("user32.dll")]
        private static extern int SendMessage(IntPtr hWnd, int Msg, int wParam, int lParam);
        [DllImportAttribute("user32.dll")]
        private static extern bool ReleaseCapture();

        private Parser p = new Parser(); //inicializace Parseru
        private MethodInfo[] metody; //inicializace třídní proměnné obsahující informace o metodách matematické knihovny
        private int startListBoxIndex;
      
        private bool adding = false;
        private bool degr = true;
        private double vysledek = 0;
        public Form1()
        {
           
            InitializeComponent();
          
          

        }

        private void TextBox1_LostFocus(object sender, EventArgs e)
        {
            textBox1.Focus();
        }

        private void ListBox1_DrawItem(object sender, DrawItemEventArgs e)
        {
            e.DrawBackground();

            Graphics g = e.Graphics;
            if (e.Index == 0)
            {
                g.FillRectangle(new SolidBrush(Color.SteelBlue), e.Bounds);
                textBox3.Visible = true;
                textBox3.Location = new Point(listBox1.Location.X + listBox1.Width, listBox1.Location.Y+  e.Bounds.Y);
                
                string methodName = listBox1.Items[0].ToString();
                methodName = methodName.Split('(')[0];
                
                foreach (MethodInfo m in metody)
                {
                    if (m.Name == methodName)
                    {
                        try
                        {
                            Popis p = (Popis)m.GetCustomAttributes(typeof(Popis), true)[0];
                            textBox3.Text = p.GetName();
                        }
                        catch (Exception ex)
                        {
                            textBox3.Text = "Žádná nápověda není k dispozici";
                        }
                    } 
                }
                Size size = TextRenderer.MeasureText(textBox3.Text, textBox3.Font);
                textBox3.Width = size.Width;
                textBox3.Height = size.Height;

                if (textBox3.Location.X + textBox3.Width > ClientSize.Width)
                {
                    textBox3.Location = new Point(listBox1.Location.X -  (textBox3.Width - listBox1.Width), listBox1.Location.Y + listBox1.Height);
                }
                if (textBox3.Location.X < 0)
                {
                    textBox3.Location = new Point(0, listBox1.Location.Y + listBox1.Height);
                }
                // get method description

            }
            else
            {
                g.FillRectangle(new SolidBrush(Color.White), e.Bounds);
            }
            ListBox lb = (ListBox)sender;
            g.DrawString(lb.Items[e.Index].ToString(), e.Font, new SolidBrush(Color.Black), new PointF(e.Bounds.X, e.Bounds.Y));

            e.DrawFocusRectangle();
        }

        /// <summary>
        /// Rekurzivně projde všechny componenty, které se nacházejí pod componentou ctrl
        /// a deaktivuje přeskakování mezi nimi pomocí tabulátoru
        /// </summary>
        /// <param name="ctrl"></param>
        private void DisableTabStop(Control ctrl)
        {
            ctrl.TabStop = false;
            foreach (Control item in ctrl.Controls)
            {
                DisableTabStop(item);
            }
        }
        private void button1_Click(object sender, EventArgs e)
        {
            this.Invoke(new MethodInvoker(vypocitat));

        }

        private void textBox1_KeyDown(object sender, KeyEventArgs e)
        {
            if (e.KeyCode == Keys.Enter)
            {
                if (listBox1.Visible)
                {
                    listBox1.SelectedIndex = 0;
                }
                else
                {
                    this.Invoke(new MethodInvoker(vypocitat));
                }

                e.SuppressKeyPress = true;
            }
            if (e.KeyCode == Keys.Tab)
            {
                if (listBox1.Visible)
                {
                    listBox1.SelectedIndex = 0; 
                }
                e.SuppressKeyPress = true;
            }
            if (e.KeyCode == Keys.Down)
            {
                if (listBox1.Visible)
                {
                    if (listBox1.Items.Count > 1)
                    {
                        startListBoxIndex++;
                        fillListBox(listBox1, startListBoxIndex);
                    }
                }
                e.SuppressKeyPress = true;
            }
            if (e.KeyCode == Keys.Up)
            {
                if (listBox1.Visible)
                {
                    if (startListBoxIndex > 0)
                    {
                        startListBoxIndex--;
                        fillListBox(listBox1, startListBoxIndex);
                    }
                    
                }
                e.SuppressKeyPress = true;
            }
            if (e.KeyCode == Keys.Escape)
            {
                if (listBox1.Visible)
                {
                    listBox1.Visible = false;
                }
            }
        }

        /// <summary>
        /// Metoda se stará o inicializaci a předání informací parseru
        /// </summary>
        private void vypocitat()
        {
            if (textBox1.Text == "")
            {
                textBox2.Text = "= 0 ";
                return;
            }
            string func = textBox1.Text;
            
            p = new Parser();
             vysledek = p.Vypocitat(func);

            textBox2.Text = double.IsNaN(vysledek) ? "Nelze spočítat " : "= " + vysledek.ToString()+" ";
        }

        private void button2_Click(object sender, EventArgs e)
        {
            AddtoTextBox("sqrt(\\)");
        }

        private void button3_Click(object sender, EventArgs e)
        {
            AddtoTextBox("max(\\;)");
        }

        /// <summary>
        /// Metoda bere jako parametr string, který uloží na index v textBoxu, kde se nachází kurzor
        /// str obsahuje zástupnou značku, která definuje, kam se má přesunout kurzor
        /// </summary>
        /// <param name="str"></param>
        private void AddtoTextBox(string str)
        {
            adding = true;
            int cursor = textBox1.SelectionStart;
            int index = str.IndexOf('\\');
            str = str.Remove(index,1);
            textBox1.Text = textBox1.Text.Insert(cursor, str);
            textBox1.SelectionStart = cursor + index;
            adding = false;
            textBox1.Focus();
        }
        /// <summary>
        /// Metoda bere jako parametr string, který uloží na index  i
        /// str obsahuje zástupnou značku, která definuje, kam se má přesunout kurzor
        /// </summary>
        /// <param name="str"></param>
        /// <param name="i"></param>
        private void AddtoTextBox(string str, int i)
        {
            adding = true;
            int cursor = i;
            int index = str.IndexOf('\\');
            str = str.Remove(index, 1);
            textBox1.Text = textBox1.Text.Insert(cursor, str);
            textBox1.SelectionStart = cursor + index;
            adding = false;
            textBox1.Focus();
        }
        private void button4_Click(object sender, EventArgs e)
        {
            if (degr)
            {
                AddtoTextBox("sin(\\)");
            }
            else
            {
                AddtoTextBox("sinr(\\)");
            }
        }

        private void button5_Click(object sender, EventArgs e)
        {
            if (degr)
            {
                AddtoTextBox("cos(\\)");
            }
            else
            {
                AddtoTextBox("cosr(\\)");
            }
        }

        private void button6_Click(object sender, EventArgs e)
        {
            if (degr)
            {
                AddtoTextBox("tan(\\)");
            }
            else
            {
                AddtoTextBox("tangr(\\)");
            }
        }

        private void button8_Click(object sender, EventArgs e)
        {
            AddtoTextBox("pow(\\;)");
        }

        private void button9_Click(object sender, EventArgs e)
        {
            textBox1.Text = "";
            textBox2.Text = "";
            textBox1.Focus();
        }

        private void button10_Click(object sender, EventArgs e)
        {
            int cursor = textBox1.SelectionStart;
            if (cursor > 0)
            {
               
                textBox1.Text = textBox1.Text.Remove(cursor-1, 1);

                textBox1.SelectionStart = cursor - 1;
                textBox1.Focus();
            }

        }

        private void button7_Click(object sender, EventArgs e)
        {
            AddtoTextBox("min(\\;)");
        }

        private void button11_Click(object sender, EventArgs e)
        {
            if (degr)
            {
                AddtoTextBox("cotg(\\)");
            }
            else
            {
                AddtoTextBox("cotgr(\\)");
            }
        }


        private void button25_Click(object sender, EventArgs e)
        {
            AddtoTextBox("1\\");
        }

        private void button24_Click(object sender, EventArgs e)
        {
            AddtoTextBox("2\\");
        }

        private void button20_Click(object sender, EventArgs e)
        {
            AddtoTextBox("3\\");
        }

        private void button17_Click(object sender, EventArgs e)
        {
            AddtoTextBox("4\\");
        }

        private void button16_Click(object sender, EventArgs e)
        {
            AddtoTextBox("5\\");
        }

        private void button15_Click(object sender, EventArgs e)
        {
            AddtoTextBox("6\\");
        }

        private void button19_Click(object sender, EventArgs e)
        {
            AddtoTextBox("7\\");
        }

        private void button18_Click(object sender, EventArgs e)
        {
            AddtoTextBox("8\\");
        }

        private void button14_Click(object sender, EventArgs e)
        {
            AddtoTextBox("9\\");
        }

        private void button22_Click(object sender, EventArgs e)
        {
            AddtoTextBox("0\\");
        }

        private void button23_Click(object sender, EventArgs e)
        {
            AddtoTextBox(",\\");
        }

        private void button21_Click(object sender, EventArgs e)
        {
            AddtoTextBox(";\\");
        }

        private void button12_Click(object sender, EventArgs e)
        {
            AddtoTextBox("ln(\\)");
        }

        private void button13_Click(object sender, EventArgs e)
        {
            AddtoTextBox("log(\\;)");
        }

        private void button28_Click(object sender, EventArgs e)
        {
            AddtoTextBox("+\\");
        }

        private void button29_Click(object sender, EventArgs e)
        {
            AddtoTextBox("-\\");
        }

        private void button26_Click(object sender, EventArgs e)
        {
            AddtoTextBox("*\\");
        }

        private void button27_Click(object sender, EventArgs e)
        {
            AddtoTextBox("/\\");
        }

        /// <summary>
        /// Metoda zaplní ListBox l názvy metod z místní proměnné typu MethodInfo
        /// </summary>
        /// <param name="l">Listbox, který naplňujeme</param>
        /// <param name="startIndex">Určuje kolik prvních nalezených prvků má metoda vynechat</param>
        private void fillListBox(ListBox l, int startIndex)
        {
            listBox1.Items.Clear();
            string func = getLastWord(textBox1.Text);
            
            for (int i = 0; i < metody.Length; i++)
            {
                if (metody[i].Name.ToLower().StartsWith(func))
                {
                    string name = metody[i].Name;
                    if (metody[i].GetParameters().Length == 1)
                    {
                        name += "(a)";
                    }
                    else
                    {
                        name += "(a;b)";
                    }
                    l.Items.Add(name);
                }
            }
            for (int j = 0; j < startIndex; j++)
            {
                l.Items.RemoveAt(0);
            }
            if (l.Items.Count > 0)
            {
                l.Visible = true;
            }
            else
            {
                l.Visible = false; 
            }
         
        }
        private void textBox1_TextChanged(object sender, EventArgs e)
        {
            textBox1.Focus();
            if (!adding)
            {
                if (textBox1.SelectionStart > 0 && char.IsLetter(textBox1.Text[textBox1.SelectionStart - 1]))
                {
                    //MessageBox.Show(textBox1.Text[textBox1.SelectionStart - 1].ToString());
                    listBox1.Items.Clear();
                    fillListBox(listBox1, 0);
                    
                    
                    Point cursor = textBox1.GetPositionFromCharIndex(textBox1.Text.Length - 1);
                    listBox1.Location = new Point(cursor.X + 30, cursor.Y + 27);
                    if (listBox1.Location.X + listBox1.Width > ClientSize.Width)
                    {
                        listBox1.Location = new Point(cursor.X - listBox1.Width + 10, cursor.Y + 60);
                    }
                  
                }
                else
                {

                    listBox1.Visible = false;
                }
            }
        }

        /// <summary>
        /// Metoda z řetězce vyhledá poslední nalezenou nepřerušenou posloupnost písmen
        /// </summary>
        /// <param name="s">Řetězec, jenž má metoda prohledat</param>
        /// <returns>Vrací řetezec obsahující poslední nepřerušenou posloupnost písmen</returns>
        private string getLastWord(string s)
        {
            string lastWord = "";

            for (int i = textBox1.SelectionStart-1; i >= 0; i--)
            {
                if (char.IsLetter(s[i]))
                {
                    lastWord += s[i];
                }
                else
                {
                    break; 
                }
               
            }
            return Reverse(lastWord);
        }

        /// <summary>
        /// Metoda převrací řetezec
        /// </summary>
        /// <param name="s">Řetězec, který má metoda převrátit</param>
        /// <returns>Vrací převrácený řetězec</returns>
        public static string Reverse(string s)
        {
            char[] charArray = s.ToCharArray();
            Array.Reverse(charArray);
            return new string(charArray);
        }

        private void listBox1_SelectedIndexChanged(object sender, EventArgs e)
        {
            if(listBox1.SelectedItem != null)
            {

                string item = listBox1.SelectedItem.ToString();
                item = item.Remove(item.IndexOf('(') + 1, 1);
                int origin = textBox1.SelectionStart - getLastWord(textBox1.Text).Length;
                adding = true;
                textBox1.Text = textBox1.Text.Remove(origin, getLastWord(textBox1.Text).Length);
                AddtoTextBox(item.Insert(item.IndexOf('(') + 1, "\\"), origin);
                listBox1.Visible = false;
                textBox3.Text = "";
                textBox3.Visible = false;
                startListBoxIndex = 0;
            }

        }

        private void listBox1_VisibleChanged(object sender, EventArgs e)
        {
            if (!listBox1.Visible)
            {
                textBox3.Visible = false;
                startListBoxIndex = 0;
               
            }
        }

        private void Form1_MouseDown(object sender, MouseEventArgs e)
        {
            if (e.Button == MouseButtons.Left)
            {
                if(e.Y < textBox1.Location.Y)
                {
                    ReleaseCapture();
                    SendMessage(Handle, WM_NCLBUTTONDOWN, HT_CAPTION, 0);
                }
              
            }
        }

        private void button31_Click(object sender, EventArgs e)
        {
            AddtoTextBox("π\\");
        }

        private void button33_Click(object sender, EventArgs e)
        {
            System.Windows.Forms.Application.Exit();
        }

        private void button34_Click(object sender, EventArgs e)
        {
            this.WindowState = FormWindowState.Minimized;
        }

        private void button35_Click(object sender, EventArgs e)
        {
            this.TopMost = !this.TopMost;
        }

        private void label1_Click(object sender, EventArgs e)
        {
        }

        private void button36_Click(object sender, EventArgs e)
        {
            ToogleFunctions();
        }
        /// <summary>
        /// Přepínání mezi rozšířenou verzí s tlačítky funkcí a minimalistickou verzí
        /// </summary>
        private void ToogleFunctions()
        {
            if (panel1.Visible)
            {
                this.Size = new Size(363, Size.Height);
                button36.Text = "Více";
                panel1.Visible = false;
            }
            else
            {
                this.Size = new Size(704, Size.Height);
                button36.Text = "Méně";
                panel1.Visible = true;
            }
        }

        private void Form1_Load(object sender, EventArgs e)
        {
            DisableTabStop(this);
            listBox1.DrawMode = DrawMode.OwnerDrawFixed;
            listBox1.DrawItem += ListBox1_DrawItem;
            metody = p.getMathFunctions();
            startListBoxIndex = 0;
            ToogleFunctions();
            textBox1.Select();
        }

        private void button37_Click(object sender, EventArgs e)
        {
            AddtoTextBox(vysledek + "\\");
        }

        private void button30_Click(object sender, EventArgs e)
        {
            AddtoTextBox("\\^2");
        }


        private void textBox1_MouseDown(object sender, MouseEventArgs e)
        {
            if (listBox1.Visible)
            {
                listBox1.Visible = false; 
            }
        }

        private void button38_Click(object sender, EventArgs e)
        {
            AddtoTextBox("fact(\\)");
        }


        private void button43_Click(object sender, EventArgs e)
        {
            AddtoTextBox("declog(\\)");
        }

        private void button44_Click(object sender, EventArgs e)
        {
            AddtoTextBox("exp(\\)");
        }

        private void button38_Click_1(object sender, EventArgs e)
        {
            degr = !degr;
            Button b = (Button)sender;
            if (degr)
            {
                b.Text = "DEG";
            }
            else
            {
                b.Text = "RAD"; 
            }
        }

        private void button12_Click_1(object sender, EventArgs e)
        {
            AddtoTextBox("(\\");
        }

        private void button13_Click_1(object sender, EventArgs e)
        {
            AddtoTextBox(")\\");
        }

        private void button32_Click(object sender, EventArgs e)
        {
            AddtoTextBox("e\\");
        }
    }
}
