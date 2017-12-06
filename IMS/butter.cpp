//butter.cpp
//Author: Martin Bobcik, xbobci00
//Date: 04.12.2017
//IMS project

//casova jednotka = 1 hodina

#include "simlib.h"
#include <ctime>
#include <string>
#include <stdio.h>

#define DEN 24

#define KAPACITA_ODSTREDIVKY 10000
#define VYSTUP_ODSTREDIVKY (KAPACITA_ODSTREDIVKY * 0.097)

#define KAPACITA_ZTLOUKACE 2000
#define VYSTUP_ZTLOUKACE (KAPACITA_ZTLOUKACE/2.1)

#define KAPACITA_BALICE 20
#define VYSTUP_BALICE (KAPACITA_BALICE *4)

#define POCET_ODSTREDIVEK 5
#define POCET_ZTLOUKACU 5
#define POCET_BALICU 500

Store VstupniSklad ("Vstupni sklad", 60000);
Store skladSmetany("sklad Smetany", 9000);
Store skladMasla  ("sklad Masla", 2000);
Store skladKostek ("Sklad zabaleneho masla", 100000000);

Store odstredivka("Odstredivka", POCET_ODSTREDIVEK);
Store ztloukac("Ztloukaci valec",POCET_ZTLOUKACU);
Store balic("Balici linka", POCET_BALICU);

Stat CasCyklu("Cas Jednoho Cyklu");
Stat mlekaZaDen("pocet zpracovanych litru mleka za jeden den");


class Cyklus : public Process{
  void Behavior(){
    double cas = Time;
    if (VstupniSklad.Used() >= KAPACITA_ODSTREDIVKY)
    {
      Enter(odstredivka,1);
      Leave(VstupniSklad, KAPACITA_ODSTREDIVKY);
      Wait(1);
      Leave(odstredivka,1);
      Enter(skladSmetany,VYSTUP_ODSTREDIVKY);
      Wait(Normal(11,2));
    }

    if(skladSmetany.Used() >= KAPACITA_ZTLOUKACE){
      Enter(ztloukac,1);
      Leave(skladSmetany, KAPACITA_ZTLOUKACE);
      Wait(1);
      Enter(skladMasla, VYSTUP_ZTLOUKACE);
      Leave(ztloukac,1);
    }

      int usedBalic = 0;
      while( !skladMasla.Empty() && !balic.Full() && skladMasla.Used() >= KAPACITA_BALICE){       
        Enter(balic,1);
        Leave(skladMasla,KAPACITA_BALICE);
        usedBalic++;
      }
                                        // oprav me
      if(usedBalic > 0){
        Wait(1);
        Enter(skladKostek, (usedBalic )* KAPACITA_BALICE*4);
        Leave(balic,usedBalic);
      }
      CasCyklu(Time - cas);

    }    
  };
int mleka = 0;
  class Auto : public Process{

    void Behavior(){
      int dovezeno = Normal(41600,500);
      Enter(VstupniSklad,dovezeno);
      mleka += dovezeno;
      if((int)Time % 24 == 0){
        mlekaZaDen(mleka);
        mleka = 0;
      }
    }
  };

  class Gener : public Event{
    void Behavior(){
      (new Auto)->Activate();
      for (int i = 0; i < 6; ++i)
      {
        (new Cyklus)->Activate();
      }

      Activate(Time+1);
    }
  };

  int main(){
    //SetOutput("vystup.txt");
    Init(0,DEN *30);
    (new Gener)->Activate();
    Run();

    VstupniSklad.Output();
    odstredivka.Output();

    skladSmetany.Output();
    ztloukac.Output();

    skladMasla.Output();
    balic.Output();

    skladKostek.Output();
    
    printf("Kostek masla prumerne za jeden den: %lu\n", skladKostek.Used() / DEN);
    printf("Kil masla prumerne za jeden den: %lu\n", skladKostek.Used()/4 / DEN);

    CasCyklu.Output();
    mlekaZaDen.Output();
  }
