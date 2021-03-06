/*
 * xbabka01 xbobci00
 * blok ktery konvertuje boolean na double
 */
package ija_proj.scheme.Items.Convertor;

import ija_proj.scheme.Items.AbstractItem;
import ija_proj.scheme.Items.val.IValue;
import ija_proj.scheme.Items.val.IValueBool;
import ija_proj.scheme.Items.val.IValueDouble;
import ija_proj.scheme.Items.val.IValueInt;

import java.util.HashMap;
import java.util.Map;

import static java.lang.Double.NaN;

public class ItemBoolToDouble extends AbstractItem {

    public static final String Description =
            "[bool] => [double]\n    porty:\n          [x -> bool]";

    public ItemBoolToDouble() {
        super("B2D");
        output = new IValueDouble();
        Map<String, IValue> inport = new HashMap<>();
        inport.put("x", new IValueBool(NaN));
        this.setInput(inport);
        IValue out = new IValueDouble(NaN);
        this.setOutput(out);
    }

    @Override
    public String getDescription() {
        return ItemBoolToDouble.Description;
    }


    @Override
    public void Execute() throws Exception {
        IValueBool temp = (IValueBool) this.getInput("x");
        double val0 = temp.getValue();

        double result = NaN;
        if (!Double.isNaN(val0)) {
            result = val0;
        }

        this.setOutput(new IValueDouble(result));
    }

    protected void setOutput(IValue output){
        IValueDouble out = (IValueDouble) output;
        this.getOutput().setValue(out.toString());
    }
}
