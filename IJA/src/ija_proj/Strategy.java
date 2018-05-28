package ija_proj;

import ija_proj.gui.GUI;
import ija_proj.gui.blok.BoundLine;
import ija_proj.gui.blok.BtnBlok;
import ija_proj.gui.blok.CirclePort;
import ija_proj.scheme.Scheme;
import javafx.event.Event;
import javafx.scene.layout.Pane;
import jdk.nashorn.internal.runtime.regexp.joni.exception.ValueException;

import javax.lang.model.type.ErrorType;
import java.security.Guard;
import java.util.List;

/**
 * Singleton
 */
public class Strategy {
    private static Strategy ourInstance = null;// new Strategy();
    private static BtnBlok oldblok = null;
    private static BtnBlok newblok = null;
    private final GUI gui;

    public static Strategy getInstance() {
        return ourInstance;
    }

    private int strategy = 0;

    //nevolat
    public Strategy(GUI gui) {
        if (ourInstance == null){
            ourInstance = this;
            this.gui = gui;
        } else {
            throw new ValueException("");
        }

    }



    public int getStrategy() {
        return strategy;
    }

    public void setStrategy(int strategy) {
        this.strategy = strategy;
    }

    public void execute(Event e, BtnBlok guiBlok, Pane plocha) {
        Scheme scheme = Scheme.getInstance();

        oldblok = newblok;
        newblok = guiBlok;

        if (strategy == 0) {
            System.out.println("NIC SPECIALNEHO NEUROB");
            strategy = 0;
        } else if (strategy == 1) { //conect from
            strategy = 2;
        } else if (strategy == 2) { //conect to
            //spoj bloky a potom ich spoj ciarou
            strategy = 0;
        } else if (strategy == 3) { // zmaz
            plocha.getChildren().remove(guiBlok);
            scheme.remove_blok(guiBlok.getBlok());
            GUI.getListblok().remove(guiBlok);
//            List<CirclePort> porty = guiBlok.getPorty().values();

            for (CirclePort port :
                    guiBlok.getPorty().values()) {
                BoundLine.disconet(port, plocha);
            }
            gui.getRight().update();

            strategy = 0;
        } else if (strategy == 4) {//execute
            scheme.start(false);
            strategy = 0;
        } else if (strategy == 5) {//krokuj

        }


    }
}
