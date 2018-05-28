/*
 * xbabka01 xbobci00
 * blok ktery konvertuje double na boolean
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

public class ItemDoubleToBool extends AbstractItem {

    public static final String Description =
            "[double] => [boot]\n    porty:\n          [x -> double]";

    public ItemDoubleToBool() {
        super("D2B");
        output = new IValueBool();
        Map<String, IValue> inport = new HashMap<>();
        inport.put("x", new IValueDouble(NaN));
        this.setInput(inport);
        IValue out = new IValueBool(NaN);
        this.setOutput(out);
    }

    @Override
    public String getDescription() {
        return ItemDoubleToBool.Description;
    }


    @Override
    public void Execute() throws Exception {
        IValueDouble temp = (IValueDouble) this.getInput("x");
        double val0 = temp.getValue();

        double result = NaN;
        if (!Double.isNaN(val0)) {
            result = val0;
        }

        this.setOutput(new IValueBool(result));
    }

    protected void setOutput(IValue output){
        IValueBool out = (IValueBool) output;
        this.getOutput().setValue(out.toString());
    }
}
