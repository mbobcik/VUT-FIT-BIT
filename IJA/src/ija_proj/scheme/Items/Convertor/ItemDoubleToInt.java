/*
 * xbabka01 xbobci00
 * blok ktery konvertuje double na int
 */
package ija_proj.scheme.Items.Convertor;

import ija_proj.scheme.Items.AbstractItem;
import ija_proj.scheme.Items.val.IValue;
import ija_proj.scheme.Items.val.IValueDouble;
import ija_proj.scheme.Items.val.IValueInt;

import java.util.HashMap;
import java.util.Map;

import static java.lang.Double.NaN;

public class ItemDoubleToInt extends AbstractItem {

    public static final String Description =
            "[double] => [int]\n    porty:\n          [x -> double]";

    public ItemDoubleToInt() {
        super("D2I");
        output = new IValueInt();
        Map<String, IValue> inport = new HashMap<>();
        inport.put("x", new IValueDouble(NaN));
        this.setInput(inport);
        IValue out = new IValueInt(NaN);
        this.setOutput(out);
    }

    @Override
    public String getDescription() {
        return ItemDoubleToInt.Description;
    }


    @Override
    public void Execute() throws Exception {
        IValueDouble temp = (IValueDouble) this.getInput("x");
        double val0 = temp.getValue();

        double result = NaN;
        if (!Double.isNaN(val0)) {
            result = val0;
        }

        this.setOutput(new IValueInt(result));
    }

    protected void setOutput(IValue output){
        IValueInt out = (IValueInt) output;
        this.getOutput().setValue(out.toString());
    }
}
