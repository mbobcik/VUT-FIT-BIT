package ija_proj.gui.blok;

import ija_proj.scheme.Items.val.IValue;
import javafx.beans.property.DoubleProperty;
import javafx.scene.control.Alert;
import javafx.scene.control.ButtonType;
import javafx.scene.control.Tooltip;
import javafx.scene.layout.Pane;
import javafx.scene.paint.Color;
import javafx.scene.shape.Line;
import javafx.scene.shape.StrokeLineCap;
import java.util.Observer;

/**
 * Spojnica medzi portami
 */
public class BoundLine extends Line {
    protected CirclePort start;
    protected CirclePort end;
    private static CirclePort save = null;
    private Tooltip tip = new Tooltip();


    private resultobserv ov = null;

    /**
     * observet portov ci sa hodnota vystupneho portu zmenila ak ano tak tuto hodnoru nastav do vystupu
     */
    class resultobserv implements Observer {
        private IValue val = null;
        private BoundLine port = null;

        private resultobserv(IValue val, BoundLine port) {
            this.val = val;
            this.port = port;
        }

        @Override
        public void update(java.util.Observable o, Object arg) {
            if (o == val) {
                port.tip.setText(start.getValue().getData().getOutput().getStringValue());
                end.getValue().getData().setInput(end.getName(), val);
            }
        }
    }

    /**
     * Odstan spojnicu z gui a odpoj hu z blokou
     * @param port port ktory ma odpojit
     * @param main Panel v ktorom sa nachadza spojnica (aby vedelo odstanit spojnicu z gui)
     */
    public static void disconet(CirclePort port, Pane main) {
        BoundLine line = port.getLine();
        if (line != null) {
            //todo odstanit aj z MNode
            line.start.getValue().setOutput(null);
            line.end.getValue().setChild(line.end.getName(), null);

            line.start.setLine(null);
            line.end.setLine(null);
            main.getChildren().remove(line);
        }
    }

    /**
     * Vrat zaciatok spojnice
     * @return zaciatok
     */
    public CirclePort getStart() {
        return start;
    }

    /**
     * Vrat koniec spojnice
     * @return koniec
     */
    public CirclePort getEnd() {
        return end;
    }

    /**
     * bytvor spojnicu vzdy ked je ulozeny port ak nie tak ho uloz
     * @param port ktory chceme spojit s predchadzujicim alebo nasledujucim
     * @param main
     */
    //todo premenovat na neco rozumne
    public static void urob(CirclePort port, Pane main) {
        if (port == null){
            save = null;
        } else if (save == port) {
            if (port.getLine() != null) {
                System.out.println("odpojujem");

                BoundLine.disconet(port, main);
                System.out.println("odpojeno");

                save = null;
            } else {
                System.out.println("nerob nic");

            }

        } else if (save == null) {
            save = port;
            System.out.println("saving");
        } else {
            if (port == null){
                save = null;
            } else if (CirclePort.conectable(port, save)) {
                System.out.println("bonding");
                BoundLine bonded = null;
                if (save.isType()){
                    bonded = new BoundLine(save, port, main);
                } else {
                    bonded = new BoundLine(port, save, main);
                }
                System.out.println("bended");

                save = null;
            } else {
                System.out.println("nespojitelne");



                save = null;

                Alert alert = new Alert(Alert.AlertType.ERROR);
                alert.setTitle("Chyba");
                alert.setHeaderText("Nespojitelné bloky!");
                alert.setContentText("Tyto bloky nemají kompatibilní typy");
                alert.showAndWait().ifPresent(rs -> {
                    if (rs == ButtonType.OK) {
                        System.out.println("Pressed OK.");
                    }
                });

            }
        }
    }

    /**
     * updatni tooltip na aktualnu hodnotu
     */
    private void update() {
        tip.setText(start.getValue().getData().getOutput().toString());
    }

    /**
     * Vytvor spojnicu
     * @param start zaciatok spojnice (musi byt output)
     * @param end koniec spojnice (musi byt input)
     * @param main Pane v ktorom sa ma trieda spojnica pridat
     */
    private BoundLine(CirclePort start, CirclePort end, Pane main) {
        Tooltip.install(this, tip);
        this.end = end;
        this.start = start;
        update();

        BoundLine.disconet(start, main);

        BoundLine.disconet(end, main);


        start.setLine(this);
        end.setLine(this);

        end.getValue().setChild(end.getName(), start.getValue());
        start.getValue().setOutput(end.getValue());


        DoubleProperty startX = start.absuluteXProperty();
        DoubleProperty startY = start.absuluteYProperty();

        DoubleProperty endX = end.absuluteXProperty();
        DoubleProperty endY = end.absuluteYProperty();

        startXProperty().bind(startX);
        startYProperty().bind(startY);
        endXProperty().bind(endX);
        endYProperty().bind(endY);
        setStrokeWidth(2);
        setStroke(Color.GRAY.deriveColor(0, 1, 1, 0.5));
        setStrokeLineCap(StrokeLineCap.BUTT);
        getStrokeDashArray().setAll(10.0, 5.0);

        main.getChildren().add(this);
        toFront();
        this.ov = new resultobserv(start.getValue().getData().getOutput(), this);

        start.getValue().getData().getOutput().addObserver(ov);

        end.getValue().getData().setInput(end.getName(),start.getValue().getData().getOutput());

    }
}
