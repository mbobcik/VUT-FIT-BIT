/*
 * xbabka01 xbobci00
 * blok ktery provede logickou operaci XOR nad vstupy a vysledek vystavi na vystupni port
 */
package ija_proj.scheme.Items.Bool;

import ija_proj.scheme.Items.AbstractItem;
import ija_proj.scheme.Items.val.IValue;
import ija_proj.scheme.Items.val.IValueBool;
import ija_proj.scheme.Items.val.IValueDouble;

import java.util.HashMap;
import java.util.Map;

import static java.lang.Double.NaN;

public class ItemBoolNot extends AbstractItem {

    public static final String Description =
            "NOT x = [bool]\n    porty:\n          [x -> bool]";

    public ItemBoolNot() {
        super("NOT x");
        output = new IValueBool();
        Map<String, IValue> inport = new HashMap<>();
        inport.put("x", new IValueBool(NaN));
        this.setInput(inport);
        IValue out = new IValueBool(NaN);
        this.setOutput(out);
    }

    @Override
    public String getDescription() {
        return ItemBoolNot.Description;
    }


    @Override
    public void Execute() throws Exception {
        IValueBool temp = (IValueBool) this.getInput("x");
        double val0 = temp.getValue();



        double result = NaN;
        if (!Double.isNaN(val0)) {
            if (Math.abs(val0) < 2 * Double.MIN_VALUE)
                result = 1.0;
            else
                result = 0.0;
        }

        this.setOutput(new IValueBool(result));
    }

    protected void setOutput(IValue output){
        IValueBool out = (IValueBool) output;
        this.getOutput().setValue(out.toString());
    }
}
