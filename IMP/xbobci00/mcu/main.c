/*******************************************************************************
   key.c: main for aplikation keyboard
   Copyright (C) 2009 Brno University of Technology,
                      Faculty of Information Technology
   Author(s): Jan Markovic <xmarko04 AT stud.fit.vutbr.cz>

   LICENSE TERMS

   Redistribution and use in source and binary forms, with or without
   modification, are permitted provided that the following conditions
   are met:
   1. Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
   2. Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in
      the documentation and/or other materials provided with the
      distribution.
   3. All advertising materials mentioning features or use of this software
      or firmware must display the following acknowledgement:

        This product includes software developed by the University of
        Technology, Faculty of Information Technology, Brno and its
        contributors.

   4. Neither the name of the Company nor the names of its contributors
      may be used to endorse or promote products derived from this
      software without specific prior written permission.

   This software or firmware is provided ``as is'', and any express or implied
   warranties, including, but not limited to, the implied warranties of
   merchantability and fitness for a particular purpose are disclaimed.
   In no event shall the company or contributors be liable for any
   direct, indirect, incidental, special, exemplary, or consequential
   damages (including, but not limited to, procurement of substitute
   goods or services; loss of use, data, or profits; or business
   interruption) however caused and on any theory of liability, whether
   in contract, strict liability, or tort (including negligence or
   otherwise) arising in any way out of the use of this software, even
   if advised of the possibility of such damage.

   $Id$


*******************************************************************************/

#include <fitkitlib.h>
#include <keyboard/keyboard.h>
#include <limits.h>
#include <stdio.h>

#define N0 0x01
#define N1 0x4F
#define N2 0x12
#define N3 0x06
#define N4 0x4C
#define N5 0x24
#define N6 0x20
#define N7 0x0F
#define N8 0x00
#define N9 0x04
#define CHAR_R 0x7A
#define CHAR_E 0x30

#define D4 0x80// P6
#define D3 0x20//P2
#define D2 0x10//P2

#define INT_DECIMAL_STRING_SIZE(int_type) ((CHAR_BIT*sizeof(int_type)-1)*10/33+3)

int tmp_freq;
int freq_toPrint;
int lock;


char last_ch; //naposledy precteny znak


char *int_to_string_alloc(int x) {
  int i = x;
  char buf[INT_DECIMAL_STRING_SIZE(int)];
  char *p = &buf[sizeof buf - 1];
  *p = '\0';
  if (i >= 0) {
    i = -i;
  }
  do {
    p--;
    *p = (char) ('0' - i % 10);
    i /= 10;
  } while (i);
  if (x < 0) {
    p--;
    *p = '-';
  }
  size_t len = (size_t) (&buf[sizeof buf] - p);
  char *s = malloc(len);
  if (s) {
    memcpy(s, p, len);
  }
  return s;
}

/*******************************************************************************
 * Vypis uzivatelske napovedy (funkce se vola pri vykonavani prikazu "help")
*******************************************************************************/
void print_user_help(void)
{
}


/*******************************************************************************
 * Obsluha klavesnice
*******************************************************************************/
int keyboard_idle()
{
  char ch;
  ch = key_decode(read_word_keyboard_4x4());
  if (ch != last_ch) // stav se zmnenil
  {
    last_ch = ch;
    if (ch != 0) // vylucime pusteni klavesy
    {
      term_send_crlf();
      term_send_str("Na klavesnici byla zmacnuta klavesa \'");
      term_send_char(ch);
      term_send_char('\'');
      term_send_crlf();
      term_send_str(" >");
      tmp_freq++;
    }
  }
  return 0;
}



/*******************************************************************************
 * Dekodovani a vykonani uzivatelskych prikazu
*******************************************************************************/
unsigned char decode_user_cmd(char *cmd_ucase, char *cmd)
{
  return CMD_UNKNOWN;
}

/*******************************************************************************
 * Inicializace periferii/komponent po naprogramovani FPGA
*******************************************************************************/
void fpga_initialized()
{
}

void set_single_digit(int digit, int position){
  
  P6OUT = 0x7F; 
  P2OUT &=0xCF;
  
  
  switch(digit){
    case 0:
    P6OUT = N0;
    break;
    case 1:
    P6OUT = N1;
    break;
    case 2:
    P6OUT = N2;
    break;
    case 3:
    P6OUT = N3;
    break;
    case 4:
    P6OUT = N4;
    break;
    case 5:
    P6OUT = N5;
    break;
    case 6:
    P6OUT = N6;
    break;
    case 7:
    P6OUT = N7;
    break;
    case 8:
    P6OUT = N8;
    break;
    case 9:
    P6OUT = N9;
    break;
    case CHAR_E:
    P6OUT = CHAR_E;
    break;
    case CHAR_R:
    P6OUT = CHAR_R;
    break; 
  }

  if (position == D2)
  {

    P2OUT |= D2;
  }else if (position == D3){
    P2OUT |= D3;
  }else if (position == D4){
    P6OUT |= D4;
  }else{
    P2OUT |= D2|D3;
    P6OUT |= D4;
  }
}

/*******************************************************************************
 * Hlavni funkce
*******************************************************************************/
int main(void)
{
  tmp_freq = 0;
  unsigned int cnt = 0;
  last_ch = 0;

  initialize_hardware();
  keyboard_init();

  set_led_d6(1);                       // rozsviceni D6
  set_led_d5(1);                       // rozsviceni D5

  WDTCTL = WDTPW + WDTHOLD; // zastav watchdog

  CCTL0 = CCIE;                             // povol preruseni pro casovac (rezim vystupni komparace) 
  CCR0 = 0x8000;                            // nastav po kolika ticich (32768 = 0x8000, tj. za 1 s) ma dojit k preruseni
  TACTL = TASSEL_1 + MC_2;                  // ACLK (f_tiku = 32768 Hz = 0x8000 Hz), nepretrzity rezim
  //set direction
  P6DIR |= 0xFF;
  P2DIR |= 0x30;

  term_send_str_crlf("ready");
  while (1)
  {
    if(freq_toPrint<12){
      set_single_digit(freq_toPrint/100,D2);
      set_single_digit((freq_toPrint%100)/10,D3);
      set_single_digit(freq_toPrint%10,D4);
    }
    else{
      set_single_digit(CHAR_E,D2);
      set_single_digit(CHAR_R,D3);
      set_single_digit(CHAR_R,D4);
    }
    terminal_idle();                   // obsluha terminalu
    keyboard_idle();
  }         
}

interrupt (TIMERA0_VECTOR) Timer_A (void)
{
  term_send_str_crlf("sekunda");
  term_send_str_crlf(int_to_string_alloc(tmp_freq));
  lock = 0;

  freq_toPrint = tmp_freq;
  tmp_freq = 0;

  CCR0 += 0x8000; 
}