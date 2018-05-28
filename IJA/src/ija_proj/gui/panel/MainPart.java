package ija_proj.gui.panel;

import ija_proj.gui.GUI;
import javafx.scene.control.ScrollPane;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.Pane;
import javafx.stage.Stage;

/**
 * plocha do ktorej sa vykresluju bloky
 */
public class MainPart extends ScrollPane {
    public void update() {

    }

    public MainPart(BorderPane border, GUI gui, Stage stage) {
        super(new Pane());
        border.setCenter(this);
        this.setStyle("-fx-background-color: #336699;");
    }
}
