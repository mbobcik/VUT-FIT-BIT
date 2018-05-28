package ija_proj.scheme.Items.val;

import static java.lang.Double.NaN;

/**
 * Trieda vyjadrujuca typ IDouble
 */
public class IValueDouble extends IValue {
    private double value;

    public IValueDouble(double value) {
        super("DOUBLE");
        this.value = value;
    }

    public IValueDouble() {
        super("DOUBLE");
        this.value = NaN;
    }

    public double getValue() {
        return value;
    }

    public String getStringValue() {
        return "" + value;
    }

    public void setValue(double value) {
        this.value = value;
    }

    @Override
    public void copyVal(IValue val) {
        if (val.equals(this)) {
            IValueDouble temp = (IValueDouble) val;
            this.value = temp.value;
        } else {
            System.err.println("INE TYPY");
            this.value = NaN;
        }
        setChanged();
        notifyObservers();
    }

    @Override
    public void setValue(String value) {
        try {
            this.value = Double.parseDouble(value);

        } catch (NumberFormatException e) {
            this.value = NaN;
        }
        setChanged();
        notifyObservers();

    }


    @Override
    public String toString() {
        return "" + value;
    }
}
