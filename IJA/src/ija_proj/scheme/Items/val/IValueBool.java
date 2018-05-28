package ija_proj.scheme.Items.val;

import static java.lang.Double.NaN;

/**
 * Trieda vyjadrujuca typ Int
 */
public class IValueBool extends IValue {
    private double value;

    public IValueBool(double value) {
        super("INT");
        double tmp =  Math.rint(value);
        if (Math.abs(tmp) < 2 * Double.MIN_VALUE)
            this.value = 0.0;
        else
            this.value = 1.0;
    }

    public IValueBool() {
        super("INT");
        this.value = 0.0;
    }

    public double getValue() {
        return value;
    }

    public String getStringValue() {
        return "" + value;
    }

    public void setValue(double value) {
        if (Math.abs(value) < 2 * Double.MIN_VALUE)
            this.value = 0.0;
        else
            this.value = 1.0;
    }

    @Override
    public void copyVal(IValue val) {
        if (val.equals(this)) {
            IValueBool temp = (IValueBool) val;
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
            double tmp =  Math.rint(Double.parseDouble(value));
            if (Math.abs(tmp) < 2 * Double.MIN_VALUE)
                this.value = 0.0;
            else
                this.value = 1.0;
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
