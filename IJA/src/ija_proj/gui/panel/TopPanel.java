package ija_proj.gui.panel;

import ija_proj.Strategy;
import ija_proj.gui.GUI;
import ija_proj.gui.blok.BoundLine;
import ija_proj.scheme.Scheme;
import javafx.geometry.Insets;
import javafx.scene.control.Alert;
import javafx.scene.control.Button;
import javafx.scene.control.ScrollPane;
import javafx.scene.layout.BorderPane;
import javafx.scene.layout.HBox;
import javafx.scene.layout.Pane;
import javafx.scene.text.Font;
import javafx.scene.text.FontWeight;
import javafx.scene.text.Text;
import javafx.stage.FileChooser;
import javafx.stage.Stage;

import java.io.File;

/**
 * Horny panel v ktorom su uvedene operacie
 */
public class TopPanel extends ScrollPane {
    private static HBox genPanel(GUI gui, Stage stage) {
        Strategy strategy = Strategy.getInstance();

        HBox panel = new HBox();
        panel.setPadding(new Insets(15, 12, 15, 12));
        panel.setSpacing(10);
        panel.setStyle("-fx-background-color: #336699;");

        Text title = new Text("Operace:");
        title.setFont(Font.font("Arial", FontWeight.BOLD, 14));
        panel.getChildren().add(title);
        panel.setPrefWidth(1197);



        /* * * * * * * * * * * * * * * * * *
        Vytvorenie operacii a ich definovanie
         * * * * * * * * * * * * * * * * * */
//        File out = null, in = null;
        //todo operacie
        Button save = new Button("Ulož");
        save.setPrefSize(100, 20);
        save.setOnMouseClicked(event -> {
            strategy.setStrategy(0);
            //todo uloz schemu
            FileChooser fileChooser = new FileChooser();
            fileChooser.setTitle("Ulož");
            File out = fileChooser.showSaveDialog(stage);
            Scheme.getInstance().save(out);
            gui.reset_color();

        });

        Button load = new Button("Načti");
        load.setPrefSize(100, 20);
        load.setOnMouseClicked(event -> {
            strategy.setStrategy(0);
            //todo nacti schemu
            FileChooser fileChooser = new FileChooser();
            fileChooser.setTitle("Načti");
            File in = fileChooser.showOpenDialog(stage);
            Scheme.getInstance().load(in);
            gui.reload();
            gui.reset_color();

        });

        Button zmaz = new Button("Zmaž blok");
        zmaz.setPrefSize(100, 20);
        zmaz.setOnMouseClicked(event -> {
            strategy.setStrategy(3);
            //todo spusti blok na ktrory klikne
            gui.reset_color();
            BoundLine.urob(null, (Pane)gui.getMain().getContent());

        });

        Button spust = new Button("Spusti");
        spust.setPrefSize(100, 20);
        spust.setOnMouseClicked(event -> {
            if (!Scheme.getInstance().start(false)){
                Alert a = new Alert(Alert.AlertType.INFORMATION);
                a.setTitle("Chyba");
                a.setHeaderText("Chyba při výpočtu!");
                a.setContentText("Zkontrolujte, zda se ve schématu nenachází cyklus.");
                a.showAndWait();
                return;
            }
            gui.update();
//            strategy.setStrategy(4);
            //todo spusti blok na ktrory klikne
            gui.reset_color();

        });
        Button krokuj = new Button("Krokuj");
        Button next = new Button("Další");
        next.setVisible(false);
        next.setOnMouseClicked(event ->{
            gui.reset_color();

            if (!Scheme.getInstance().next()){
                System.out.println("KONEC VYPOCTU");
                Alert stop = new Alert(Alert.AlertType.INFORMATION);
                stop.setContentText("KONEC VYPOCTU");
                stop.setHeaderText(null);

                stop.showAndWait();


                save.setVisible(!save.isVisible());
                load.setVisible(!load.isVisible());
                spust.setVisible(!spust.isVisible());
                zmaz.setVisible(!zmaz.isVisible());
                next.setVisible(!next.isVisible());

                gui.getLeft().setVisible(!gui.getLeft().isVisible());
                gui.getRight().setVisible(!gui.getRight().isVisible());
                krokuj.setText("Krokuj");
            }
        });

        krokuj.setPrefSize(100, 20);
        krokuj.setOnMouseClicked(event -> {
            save.setVisible(!save.isVisible());
            load.setVisible(!load.isVisible());
            spust.setVisible(!spust.isVisible());
            zmaz.setVisible(!zmaz.isVisible());
            next.setVisible(!next.isVisible());

            gui.getLeft().setVisible(!gui.getLeft().isVisible());
            gui.getRight().setVisible(!gui.getRight().isVisible());

            if (!save.isVisible()){
                if (!Scheme.getInstance().start(true)){
                    Alert a = new Alert(Alert.AlertType.INFORMATION);
                    a.setTitle("Chyba");
                    a.setHeaderText("Chyba při výpočtu!");
                    a.setContentText("Zkontrolujte, zda se ve schématu nenachází cyklus.");
                    save.setVisible(!save.isVisible());
                    load.setVisible(!load.isVisible());
                    spust.setVisible(!spust.isVisible());
                    zmaz.setVisible(!zmaz.isVisible());
                    next.setVisible(!next.isVisible());

                    gui.getLeft().setVisible(!gui.getLeft().isVisible());
                    gui.getRight().setVisible(!gui.getRight().isVisible());
                    a.showAndWait();
                    krokuj.setText("Krokuj");

                    return;
                }
                krokuj.setText("Stop");

            } else {
                krokuj.setText("Krokuj");
//                gui.update();
            }
//            strategy.setStrategy(5);
//            //todo spusti blok na ktrory klikne

            gui.reset_color();

        });



        /* * * * * * * * * * * * * * * * * *
                Pridanie operacii do panela
         * * * * * * * * * * * * * * * * * */
        panel.getChildren().addAll(save, load, zmaz, spust, krokuj,next);
        return panel;
    }

    public TopPanel(BorderPane main, GUI gui, Stage stage) {
        super(TopPanel.genPanel(gui, stage));
        main.setTop(this);
        this.setStyle("-fx-background-color: #336699;");


    }

}
