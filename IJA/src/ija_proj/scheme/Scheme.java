/*
 * xbabka01 xbobci00
 * schema ktora urcuje ako sa maju spajat bloky a sluzi k ulozenie a nacitaniu
 */
package ija_proj.scheme;

import ija_proj.Executor;

import java.io.*;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;

/**
 * Trieda tvoriaca schemu (singleton)
 * @author peter
 */
public class Scheme {

    /**
     * Premenna vyjadrujuca instanciu singletona
     */
    private static Scheme ourInstance = new Scheme();
    private List<MNode> itemList;
    private Executor executor = null;

    /**
     * Vrati instatnciu Schemy
     * @return ourInstance
     */
    public static Scheme getInstance() {
        return ourInstance;
    }

    /**
     * Vrati executor ktory je aktivny
     * @return
     */
    public Executor getExecutor() {
        return executor;
    }

    /**
     * vrati List blokou ktore su aktivne v scheme
     * @return itemList
     */
    public List<MNode> getItemList() {
        return itemList;
    }

    /**
     * vymaze vsetky vloky zo schemy
     */
    public void reset(){
        this.itemList.clear();
    }

    /**
     * Vytvori schemu s 0 blokamy
     */
    private Scheme() {
        this.itemList = new ArrayList<>();
        this.executor = new Executor(this.itemList);
    }

    /**
     * prida blok do schemy
     * @param blok ktory sa pridava
     */
    public void add_block(MNode blok) {
        this.itemList.add(blok);
    }

    /**
     * odstany blok z schemy
     * @param blok ktory sa ma odstanit
     */
    public void remove_blok(MNode blok) {
        this.itemList.remove(blok);
    }

    /**
     * ked ma krokovat tak len vytvori len executor ak nie tak aj vykona
     * @param step bool ktory vyjadruje ci mam krokovat true -> krokuj inak vykonaj
     */
    public boolean start(boolean step){
        return this.executor.start(step);
    }

    /**
     * Vykonaj dalsi blok
     * nevykona nic pokial uz vykonal vsetky bloky v executery
     */
    public boolean next(){
        return this.executor.next();
    }


    /**
     * Uloz bloky do suboru
     * @param addres subor do ktoreho sa maju aktialne bloky ulozit
     * @return ci bola operace uspesna
     */
    public boolean save(File addres/* parameter kam sa ma ulozit*/) {
        System.out.println("SAVE");
        try {
            FileOutputStream out = new FileOutputStream(addres);
            ObjectOutputStream oout = new ObjectOutputStream(out);

            oout.writeObject(this.itemList);
            oout.flush();
            oout.close();
            out.close();
            return true;
        } catch (Exception e){
            System.err.println("err");
            System.err.println(e.toString());
            return false;
        }


    }

    /**
     * Nacti bloky zo suboru
     * @param addres subor zo ktoreho sa maju aktialne bloky nacitat
     * @return ci bola operace uspesna
     */
    public boolean load(File addres/*parameter z kadial sa ma nacitat*/) {
        System.out.println("LOAD");
        try {
            FileInputStream in =  new FileInputStream(addres);
            ObjectInputStream oin = new ObjectInputStream(in);

            this.getItemList().clear();

            this.getItemList().addAll((List<MNode>) oin.readObject());


            oin.close();
            in.close();
            return true;
        } catch (Exception e){
            System.err.println("err");
            System.err.println(e.toString());
            return false;
        }


    }

}
