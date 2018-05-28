/*
 * xbabka01 xbobci00
 * sprava grafickeho rozhrania
 */
package ija_proj.gui;


import ija_proj.Executor;
import ija_proj.Strategy;
import ija_proj.gui.blok.BoundLine;
import ija_proj.gui.blok.BtnBlok;
import ija_proj.gui.panel.LeftSidePanel;
import ija_proj.gui.panel.MainPart;
import ija_proj.gui.panel.RightSidePanel;
import ija_proj.gui.panel.TopPanel;
import ija_proj.scheme.MNode;
import ija_proj.scheme.Scheme;
import javafx.application.Application;
import javafx.scene.Scene;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.Pane;
import javafx.stage.Stage;

import java.util.ArrayList;
import java.util.List;


/**
 * GRAFICKE ROZHRANIE PRE APLIKACIU
 */
public class GUI extends Application {

    private static List<BtnBlok> listblok = new ArrayList<>();
    private  static TopPanel top = null;
    private  static LeftSidePanel left = null;
    private  static RightSidePanel right = null;
    private  static MainPart main = null;

    public void reload(){
        Pane pane = (Pane) main.getContent();
        pane.getChildren().clear();

        listblok.clear();
        Scheme scheme = Scheme.getInstance();

        List<MNode> nodes = new ArrayList<>();
        for (MNode node:
             scheme.getItemList()) {
            BtnBlok blok = new BtnBlok(node,this);
            listblok.add(blok);
            nodes.add(node);
        }


        for (MNode parent :
                nodes) {
            int parIndex = nodes.indexOf(parent);
            for (String key :
                    parent.getInputs().keySet()) {
                MNode child = parent.getInputs().get(key);
                if (child != null){
                    int childIndex = nodes.indexOf(child);
                    BoundLine.urob(listblok.get(parIndex).getPorty().get(key),pane);
                    BoundLine.urob(listblok.get(childIndex).getPorty().get(""),pane);
                }
            }

        }


    }

    public void update() {
        right.update();
        main.update();
    }

    public void reset_color(){
        for (BtnBlok blok :
                listblok) {
            blok.update();
        }
    }

    public TopPanel getTop() {
        return top;
    }

    public LeftSidePanel getLeft() {
        return left;
    }

    public RightSidePanel getRight() {
        return right;
    }

    public MainPart getMain() {
        return main;
    }

    public static List<BtnBlok> getListblok() {
        return listblok;
    }

    @Override
    public void start(Stage stage) {
        Pane pane = new Pane();
        BorderPane border = new BorderPane();
        new Strategy(this);

        main = new MainPart(border, this, stage);
        top = new TopPanel(border, this, stage);
        right = new RightSidePanel(border, this, stage);
        left = new LeftSidePanel(border, this, stage);

        Scene scene = new Scene(border, 1200, 777);
        stage.setScene(scene);
        stage.setResizable(false);
        stage.show();
    }

    public static void main(String args[]) {
        launch(args);
    }
}
