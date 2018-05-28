/*
 * xbabka01 xbobci00
 * blok ktery provede logickou operaci AND nad vstupy a vysledek vystavi na vystupni port
 */
package ija_proj.scheme.Items.Bool;

import ija_proj.scheme.Items.AbstractItem;
import ija_proj.scheme.Items.val.IValue;
import ija_proj.scheme.Items.val.IValueBool;
import ija_proj.scheme.Items.val.IValueDouble;

import java.util.HashMap;
import java.util.Map;

import static java.lang.Double.NaN;

public class ItemBoolAnd extends AbstractItem {

    public static final String Description =
            "x & y = [bool]\n    porty:\n          [x -> bool]\n          [y -> bool]";

    public ItemBoolAnd() {
        super("x AND y");
        output = new IValueBool();
        Map<String, IValue> inport = new HashMap<>();
        inport.put("x", new IValueBool(NaN));
        inport.put("y", new IValueBool(NaN));
        this.setInput(inport);
        IValue out = new IValueBool(NaN);
        this.setOutput(out);
    }

    @Override
    public String getDescription() {
        return ItemBoolAnd.Description;
    }


    @Override
    public void Execute() throws Exception {
        IValueBool temp = (IValueBool) this.getInput("x");
        double val0 = temp.getValue();

        temp = (IValueBool) this.getInput("y");
        double val1 = temp.getValue();


        double result = NaN;
        if (!Double.isNaN(val0) && !Double.isNaN(val1)) {
            if ((Math.abs(val0) < 2 * Double.MIN_VALUE) || (Math.abs(val1) < 2 * Double.MIN_VALUE))
                result = 0.0;
            else
                result = 1.0;
        }

        this.setOutput(new IValueBool(result));
    }

    protected void setOutput(IValue output){
        IValueBool out = (IValueBool) output;
        this.getOutput().setValue(out.toString());
    }
}
