package ija_proj.gui.panel;

import ija_proj.gui.GUI;
import ija_proj.scheme.MNode;
import ija_proj.scheme.Scheme;
import javafx.geometry.Insets;
import javafx.scene.control.Accordion;
import javafx.scene.control.ScrollPane;
import javafx.scene.control.TextField;
import javafx.scene.control.TitledPane;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.HBox;
import javafx.scene.layout.Pane;
import javafx.scene.layout.VBox;
import javafx.scene.shape.Line;
import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;
import javafx.scene.text.Text;
import javafx.stage.Stage;

/**
 * Pravy panel v ktorom mozeme nainicailizovat hodnoty portou ktore niesu spojene
 */
public class RightSidePanel extends ScrollPane {
    /**
     * Trieda vyjadrujuca jeden blok v textovom formate
     */
    private static class vstupy extends TitledPane {
        /**
         * Vytvory textovu reprezentaciu bloku zodpovedajucemu node
         * @param noda blok
         */
        public vstupy(MNode noda) {
            super();
            this.setText("[" + noda.getId() + "] " + noda.getData().getName());
            VBox box = new VBox();
            for (String key :
                    noda.getData().getInput().keySet()) {
                HBox inbox = new HBox();
                Text text = new Text(key + " : ");
                inbox.getChildren().add(text);
                if (noda.getInputs().get(key) == null) {
                    TextField textField = new TextField(noda.getData().getInput(key).getStringValue());
                    inbox.getChildren().add(textField);
                    textField.textProperty().addListener(observable -> {
                        noda.getData().getInput(key).setValue(textField.getText());

                    });
                } else {
                    Text textField = new Text(noda.getData().getInput(key).getStringValue());
                    inbox.getChildren().add(textField);
                }
                this.setContent(box);
                box.getChildren().add(inbox);

            }




        }
    }

    /**
     * refresh praveho panal
     * zahodi stare bloky a vygeneruje nove podla schemy
     */
    public void update() {
        VBox panel = (VBox) this.getContent();
        panel.getChildren().clear();

        Text title = new Text("Bloky:");
        title.setFont(Font.font("Arial", FontWeight.BOLD, 14));
        panel.getChildren().add(title);

        Accordion accordion = new Accordion();
        for (MNode node :
                Scheme.getInstance().getItemList()) {
            TitledPane temp = new vstupy(node);
            accordion.getPanes().add(temp);
        }
        panel.getChildren().add(accordion);

    }

    /**
     * vytvory vnutro panelu
     * @param win coho je detatom
     * @param gui do akej aplikacii patri
     * @return vbox ktory tvory vnutro praveho panelu
     */
    private static VBox genPanel(BorderPane win, GUI gui) {
         /* * * * * * * * * * * * * * * * * *
                Nastavenie panela
         * * * * * * * * * * * * * * * * * */
        ScrollPane main = (ScrollPane) win.getCenter();
        Pane pane = (Pane) main.getContent();
        Scheme scheme = Scheme.getInstance();
        VBox panel = new VBox();
        panel.setPadding(new Insets(10));
        panel.setSpacing(8);
        panel.setStyle("-fx-background-color: WHITE");
        panel.setPrefHeight(640);
        panel.setPrefWidth(230);

        ScrollPane scrollPane = new ScrollPane(panel);
        scrollPane.setStyle("-fx-background-color: #336699;");

        return panel;
    }

    /**
     * Vytvor Pravy panel
     * @param window rodic panelu
     * @param gui aplikacia do ktorej patri
     */
    public RightSidePanel(BorderPane window, GUI gui, Stage stage) {
        super(genPanel(window, gui));
        window.setRight(this);
        this.update();
        this.setStyle("-fx-background-color: #336699;");
    }
}
