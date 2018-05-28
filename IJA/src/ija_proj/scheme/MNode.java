package ija_proj.scheme;

import ija_proj.scheme.Items.Item;
import ija_proj.scheme.Items.val.IValue;
import ija_proj.scheme.Items.val.IValueDouble;

import java.util.HashMap;
import java.util.Map;
import java.util.Observable;

/**
 * Trieda vyjadrujuca blok vo scheme obsahuje item ktory sa ma vykonavat a jeho blizke okoli
 */
public class MNode extends Observable implements  java.io.Serializable{

    private static int genID = 0;       /// novy index

    private Item data;                  /// Item ktory sa ma vykonavat
    private int id;                     /// inikatne id bloku
    private Map<String, MNode> inputs;  /// urcuje s cim su spojene vstupy (null -> nieje spojene)
    private MNode output;               /// urcuje s akym blokom je vystup spojen
    private double x;                   /// x suradnica v gui (pre obnovu nacitanie)
    private double y;                   /// y suradnica v gui (pre obnovu nacitanie)

    /**
     * Nastav vystup
     * @param output kam ide vystup
     */
    public void setOutput(MNode output) {
        this.output = output;
    }


    /**
     * vygenereuj unikatne id
     * problem ak bolo viacej blokou ako maxint
     * @return unikatne id
     */
    private static int genID() {
        return genID++; //nepredpokladam ze bude naraz vic blokou ako je maxint
    }

    /**
     * Vytvori mnode podla zadanych suradnic a Itemu
     * @param data item ktory vyjadruje operaciu
     * @param x suradnica v gui
     * @param y suradnica v gui
     */
    public MNode(Item data, double x, double y) {
        Scheme temp = Scheme.getInstance();
        this.data = data;
        this.id = genID();


        this.inputs = new HashMap<>();
        for (String key : data.getInput().keySet()) {
            this.inputs.put(key, null);
        }
        this.output = null;
        this.x = x;
        this.y = y;
        temp.add_block(this);
    }

    /**
     * nastav odkial ma brat vstup
     * @param name ktory vstup ma nastavit
     * @param blok aku hodnotu mu ma dat (s cim je spojen)
     */
    public void setChild(String name, MNode blok) {
        Scheme scheme = Scheme.getInstance();

        if (this.inputs.containsKey(name)) {
            MNode node = this.inputs.get(name);

            this.inputs.put(name, blok);
            setChanged();
            this.notifyObservers("SET");

        }
    }

    /**
     * Vykonaj Item data a oboznam o tom gui
     */
    public void execute() throws Exception {
        setChanged();
        this.notifyObservers("START");
        this.data.Execute();
//        setChanged();
//        this.notifyObservers("STOP");
    }

    /**
     * vrat item ktory tvory blok
     * @return data
     */
    public Item getData() {
        return data;
    }

    /**
     * Vrat id bloku
     * @return id
     */
    public int getId() {
        return id;
    }

    /**
     * Vrat vstupy
     * @return input
     */
    public Map<String, MNode> getInputs() {
        return inputs;
    }

    /**
     * Vrat output
     * @return output
     */
    public MNode getOutput() {
        return output;
    }

    /**
     * daj x suradnicu
     * @return x
     */
    public double getX() {
        return x;
    }

    /**
     * daj y suradnicu
     * @return y
     */
    public double getY() {
        return y;
    }

    /**
     * nastav suradnicu
     * @param x suradnicu
     */
    public void setX(double x) {
        this.x = x;
    }

    /**
     * nastav suradnicu
     * @param x suradnicu
     */
    public void setY(double y) {
        this.y = y;
    }
}
