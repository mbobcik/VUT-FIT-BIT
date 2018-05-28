package ija_proj.gui.panel;

import ija_proj.gui.GUI;
import ija_proj.gui.blok.BtnBlok;
import ija_proj.scheme.Items.*;
import javafx.geometry.Insets;
import javafx.scene.control.Button;
import javafx.scene.control.ScrollPane;
import javafx.scene.input.MouseButton;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.Pane;
import javafx.scene.layout.VBox;
import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;
import javafx.scene.text.Text;
import javafx.stage.Stage;

import java.util.Map;

/**
 * trieda reprezentujuca lavy panel v gui ktory obsahuje vytvarace blokou
 */
public class LeftSidePanel extends ScrollPane {
    /**
     * funckia ktora inicializuje lavy panel
     * @param window
     * @param gui
     * @return
     */
    private static VBox genPanel(BorderPane window, GUI gui) {
        ScrollPane main = (ScrollPane) window.getCenter();
        Pane pane = (Pane) main.getContent();
        RightSidePanel rightSidePanel = (RightSidePanel) window.getRight();
        /* * * * * * * * * * * * * * * * * *
                Nastavenie panela
         * * * * * * * * * * * * * * * * * */
        VBox panel = new VBox();
        panel.setPadding(new Insets(10));
        panel.setSpacing(8);
        panel.setStyle("-fx-background-color: #336699");
        panel.setPrefHeight(640);
        panel.setPrefWidth(140);




        /* * * * * * * * * * * * * * * * * *
             Pridanie tlacitiel blokou
         * * * * * * * * * * * * * * * * * */
        Text title = new Text("Bloky:");
        title.setFont(Font.font("Arial", FontWeight.BOLD, 14));
        panel.getChildren().add(title);


        /* * * * * * * * * * * * * * * * * *
              Definicia tlacidiel blokou
         * * * * * * * * * * * * * * * * * */

        Map<String, ItemFactory.genInterface> generators  = ItemFactory.gen_constructors();
        for (String key :
                generators.keySet()) {
            Button option = new Button(key);

            option.setPrefSize(100, 20);
            VBox.setMargin(option, new Insets(0, 0, 0, 8));
            panel.getChildren().add(option);



            option.setOnMouseClicked(event ->{
                if (event.getButton() == MouseButton.PRIMARY) {
                    ItemFactory.genInterface gen = generators.get(key);
                    GUI.getListblok().add(new BtnBlok(gen.generator(), gui));
                    gui.update();
                    gui.reset_color();

                }
            });
        }


        return panel;


    }


    public LeftSidePanel(BorderPane window, GUI gui, Stage stage) {
        super(LeftSidePanel.genPanel(window, gui));
        window.setLeft(this);
        this.setStyle("-fx-background-color: #336699;");
    }


}
