package ija_proj.gui;


import ija_proj.gui.panel.RightSidePanel;
import ija_proj.scheme.MNode;

import java.util.Observable;
import java.util.Observer;

/**
 * observer ktory je urceny na update praveho panelu ked nastala zmena v blokch
 */
public class DetectorOfChange implements Observer {

    private MNode ov = null;
    private RightSidePanel panel = null;

    public DetectorOfChange(MNode ov, RightSidePanel panel) {
        this.ov = ov;
        this.panel = panel;
    }

    @Override
    public void update(Observable o, Object arg) {
        System.out.println("--- --- --- change --- --- ---");

        if (o == ov && arg == "SET") {
            System.out.println("UPDATE");
            panel.update();
        }

    }
}
