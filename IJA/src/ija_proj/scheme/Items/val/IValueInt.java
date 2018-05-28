package ija_proj.scheme.Items.val;

import static java.lang.Double.NaN;

/**
 * Trieda vyjadrujuca typ Int
 */
public class IValueInt extends IValue {
    private double value;

    public IValueInt(double value) {
        super("INT");
        this.value = Math.rint(value);
    }

    public IValueInt() {
        super("INT");
        this.value = 0;
    }

    public double getValue() {
        return value;
    }

    public String getStringValue() {
        return "" + value;
    }

    public void setValue(double value) {
        this.value = Math.rint(value);
    }

    @Override
    public void copyVal(IValue val) {
        if (val.equals(this)) {
            IValueInt temp = (IValueInt) val;
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
            this.value = Math.rint(Double.parseDouble(value));
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
