package ija_proj.gui.blok;

import ija_proj.Strategy;
import ija_proj.gui.DetectorOfChange;
import ija_proj.gui.DetectorOfExecution;
import ija_proj.gui.GUI;
import ija_proj.gui.panel.RightSidePanel;
import ija_proj.scheme.Items.Item;
import ija_proj.scheme.MNode;
import javafx.scene.control.Button;
import javafx.scene.control.ScrollPane;
import javafx.scene.control.Tooltip;
import javafx.scene.layout.AnchorPane;
import javafx.scene.layout.Pane;
import javafx.scene.layout.VBox;
import javafx.scene.paint.Color;
import javafx.scene.shape.Line;
import  javafx.scene.text.Text;
import java.awt.*;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * Trieda znazornujuca blok vo GUI
 */
public class BtnBlok extends AnchorPane {
    private MNode blok;
    private Map<String ,CirclePort> porty;
    private Button main = null;


    /**
     * Navrat implicitnu farbu do bloku
     */
    public void update() {
        main.setStyle("-fx-background-color: deepskyblue;" +
                "-fx-border-color: #336699");
    }

    /**
     * vrati blok ktory vyjadruje
     * @return blok
     */
    public MNode getBlok() {
        return blok;
    }

    /**
     * vrat mapu portou
     * "" je output ostatne input
     * @return hasmap vyjadrujuci porty
     */
    public Map<String ,CirclePort> getPorty() {
        return porty;
    }

    private DetectorOfChange ov = null;

    /**
     * Inicializator grafickeho vyjadrenia bloku
     * @param blok klok ktory vykresluje
     * @param gui aplikacia do ktorej patri
     */
    private void init(MNode blok, GUI gui){
        Pane bindto = (Pane) gui.getMain().getContent();
        porty = new HashMap<>();

        int size = 90;

        this.setLayoutX(blok.getX());
        this.setLayoutY(blok.getY());
        this.setPrefSize(size, size);
        main = new Button(blok.getData().getName());
        main.setPrefSize(size-8, size-8);
        main.setLayoutX(4);
        main.setLayoutY(4);
        main.toBack();
        main.setFocusTraversable(false);
        main.setStyle("-fx-background-color: deepskyblue;" +
                "-fx-border-color: #336699");


        this.getChildren().add(main);


        bindto.getChildren().add(this);
        this.blok = blok;
        ov = new DetectorOfChange(this.blok, gui.getRight());

        Text text = new Text("["+this.blok.getId()+"]");


        VBox box = new VBox(text);
        text.setFill(Color.WHITE);
        box.setLayoutX(11);
        box.setLayoutY(0);
        box.setStyle("-fx-background-color: #336699;");

        this.getChildren().add(box);

        this.blok.addObserver(ov);

        this.blok.addObserver(new DetectorOfExecution(this.blok, this, main));

        main.setTooltip(new Tooltip("ID: " + this.blok.getId() + "\nPopis:\n" + blok.getData().getDescription()));

        Strategy strategia = Strategy.getInstance();

        int i = 0;
        for (String name :
                blok.getData().getInput().keySet()) {
            CirclePort circle = new CirclePort(name, this.blok, main.getLayoutX(), main.getLayoutY() + 20 * i++ + 15, bindto, this);
            porty.put(name,circle);
            this.getChildren().add(circle);

        }


        main.setPrefHeight(20 * --i + 30);
        this.setPrefHeight(20 * i + 40);

        CirclePort out = new CirclePort(this.blok, main.getLayoutX() + main.getPrefWidth() - 1, main.getLayoutY() + 10 * i + 16, bindto, this);
        porty.put("",out);

        this.getChildren().add(out);

        main.setOnMouseClicked(event -> {
            //todo eventy ked sa ma neco stat zmazat spojit spustit ...
            strategia.execute(event, this, bindto);
        });

        main.setOnMouseDragged(e -> {
            if (e.isPrimaryButtonDown() && strategia.getStrategy() == 0) {
                double x = e.getSceneX() - this.getWidth() / 2 - gui.getMain().getLayoutX();
                double y = e.getSceneY() - this.getHeight() / 2 - gui.getMain().getLayoutY();
                x = x < 0 ? 0 : x;
                y = y < 0 ? 0 : y;

                this.blok.setX(x);
                this.blok.setY(y);

                this.setLayoutX(x);
                this.setLayoutY(y);
                this.toFront();

                for (CirclePort port :
                        porty.values()) {
                    port.setAbsuluteX(x + port.getCenterX());
                    port.setAbsuluteY(y + port.getCenterY());
                    Line a = port.getLine();
                    if (a != null) {
                        a.toFront();
                    }
                }
            }

        });
    }

    /**
     * Inicializuij z bloku
     * @param blok MNode
     * @param gui app
     */
    public BtnBlok(MNode blok, GUI gui){
        super();
        init(blok,gui);


    }
    /**
     * Inicializuij z Itemu
     * @param blok MNode
     * @param gui app
     */
    public BtnBlok(Item blok, GUI gui) {
        super();
        init(new MNode(blok,0,0),gui);
    }


}