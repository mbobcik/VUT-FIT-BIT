package ija_proj.gui;

import ija_proj.gui.blok.BtnBlok;
import ija_proj.scheme.MNode;
import javafx.scene.control.Button;

import java.util.Observable;
import java.util.Observer;

/**
 * observer ktory meni ffarbu bloku ak bol spusteny
 */
public class DetectorOfExecution implements Observer {
    private MNode node = null;
    private BtnBlok blok = null;
    private Button btn = null;
    private static javafx.scene.paint.Color farba = null;

    public DetectorOfExecution(MNode node, BtnBlok blok, Button btn) {
        this.node = node;
        this.blok = blok;
        this.btn = btn;
    }

    @Override
    public void update(Observable o, Object arg) {
        System.out.println("--- --- --- exec --- --- ---");
        if (o == node){
            if (arg == "START") {
                System.out.println("START");
                btn.setStyle("-fx-background-color: RED;" +
                        "-fx-border-color: #336699");

            }
        }
    }
}
