package ija_proj.gui.blok;

import ija_proj.scheme.Items.val.IValue;
import ija_proj.scheme.MNode;
import javafx.beans.property.DoubleProperty;
import javafx.beans.property.SimpleDoubleProperty;
import javafx.scene.control.Tooltip;
import javafx.scene.layout.AnchorPane;
import javafx.scene.layout.Pane;
import javafx.scene.paint.Color;
import javafx.scene.shape.Circle;

import java.util.Observer;

/**
 * Trieda vyjadrujuca graficky port bloku
 */
public class CirclePort extends Circle {
    protected Tooltip tip;


    private BoundLine line = null;
    private String name = null;
    private String Printname = null;
    private MNode value = null;
    private IValue hod = null;
    private boolean type = false;

    private DoubleProperty absuluteX = null;
    private DoubleProperty absuluteY = null;
    private resultobserv ov = null;

    /**
     * observer ktory pozoruje dany port a aktualizuje tooltip
     */
    class resultobserv implements Observer {
        private IValue val = null;
        private CirclePort port = null;

        public resultobserv(IValue val, CirclePort port) {
            this.val = val;
            this.port = port;
        }

        @Override
        public void update(java.util.Observable o, Object arg) {
            if (o == val) {
                port.tip.setText(name + hod);
            }
        }
    }

    /**
     * Nastav suradnicu x relaticnu k redicu bloku
     * @param absuluteX x
     */
    public void setAbsuluteX(double absuluteX) {
        this.absuluteX.set(absuluteX);
    }

    /**
     * Nastav suradnicu y relaticnu k redicu bloku
     * @param absuluteY y
     */
    public void setAbsuluteY(double absuluteY) {
        this.absuluteY.set(absuluteY);
    }

    /**
     * Vrat hodnotu x
     * @return x
     */
    public double getAbsuluteX() {
        return absuluteX.get();
    }

    /**
     * Vrat hodnotu x ku ktorej sa ide napojit
     * @return x
     */
    public DoubleProperty absuluteXProperty() {
        return absuluteX;
    }
    /**
     * Vrat hodnotu y
     * @return y
     */
    public double getAbsuluteY() {
        return absuluteY.get();
    }

    /**
     * Vrat hodnotu y ku ktorej sa ide napojit
     * @return y
     */
    public DoubleProperty absuluteYProperty() {
        return absuluteY;
    }

    /**
     * Vrat spojnicu spojenu s portom
     * @return line
     */
    public BoundLine getLine() {
        return line;
    }

    /**
     * Vrat meno portu
     * @return meno
     */
    public String getName() {
        return Printname;
    }

    /**
     * Vrat blok ktoremu port parti
     * @return value
     */
    public MNode getValue() {
        return value;
    }

    /**
     * Vrat hodnotu ktoru reprezentuje
     * @return
     */
    public IValue getHod() {
        return hod;
    }

    /**
     * nastav line
     * @param line spojnica
     */
    public void setLine(BoundLine line) {
        this.line = line;
    }

    /**
     * metoda ktora zistup=je ci porty A a B
     * @param A port ktory overujeme
     * @param B port ktory overujeme
     * @return true su spojitelne false nespojitelne
     */
    public static boolean conectable(CirclePort A, CirclePort B) {
        boolean portType = A.type ^ B.type;
        boolean valType = A.getHod().equals(B.getHod());
        return portType && valType;
    }

    /**
     * vrati typ
     * false -> input
     * true -> output
     * @return type
     */
    public boolean isType() {
        return type;
    }

    /**
     * Vytvor port reprezentujuci vstup
     * @param name meno vstupu
     * @param value kbklok ktoremu patri
     * @param x suradnice bloku relativne k rodicu
     * @param y suradnice bloku relativne k rodicu
     * @param main platno v ktorom sa bloky pridavaju
     * @param parent rodic portu do ktoreho sa vykresluje blok
     */
    public CirclePort(String name, MNode value, double x, double y, Pane main, AnchorPane parent) {
        super(x, y, 5, Color.LIGHTCORAL);
        this.type = false;
        this.Printname = name;
        this.name = name.concat(" = ");
        this.toFront();


        absuluteX = new SimpleDoubleProperty(x+parent.getLayoutX());
        absuluteY = new SimpleDoubleProperty(y+parent.getLayoutY());

        absuluteX.add(parent.layoutXProperty());
        absuluteY.add(parent.layoutYProperty());

        tip = new Tooltip(this.name + value.getData().getInput(name));
        Tooltip.install(this, tip);
        this.value = value;
        this.hod = value.getData().getInput(name);
        this.ov = new resultobserv(value.getData().getInput(name), this);
        value.getData().getInput(name).addObserver(ov);

        this.setOnMouseClicked(event -> {
            System.out.println("KLIK na PORT");
            BoundLine.urob(this, main);
        });
    }

    /**
     * Vytvor port reprezentujuci vystup
     * @param value kbklok ktoremu patri
     * @param x suradnice bloku relativne k rodicu
     * @param y suradnice bloku relativne k rodicu
     * @param main platno v ktorom sa bloky pridavaju
     * @param parent rodic portu do ktoreho sa vykresluje blok
     */
    public CirclePort(MNode value, double x, double y, Pane main, AnchorPane parent) {
        super(x, y, 5, Color.LIGHTBLUE);
        this.Printname = "";
        this.name = "";
        this.type = true;

        tip = new Tooltip(name + value.getData().getOutput());
        Tooltip.install(this, tip);
        this.value = value;

        this.hod = value.getData().getOutput();

        absuluteX = new SimpleDoubleProperty(x+parent.getLayoutX());
        absuluteY = new SimpleDoubleProperty(y+parent.getLayoutY());

        absuluteX.add(parent.layoutXProperty());
        absuluteY.add(parent.layoutYProperty());


        this.setOnMouseClicked(event -> {
            System.out.println("KLIK na PORT");
            BoundLine.urob(this, main);
        });
        this.ov = new resultobserv(value.getData().getOutput(), this);
        value.getData().getOutput().addObserver(ov);

    }

    /**
     * update tooltip
     */
    public void update() {
        tip.setText(name + hod);
    }

    /**
     * vrat hodnotu tooltipu
     */
    public String getTip() {
        return tip.getText();
    }
}
