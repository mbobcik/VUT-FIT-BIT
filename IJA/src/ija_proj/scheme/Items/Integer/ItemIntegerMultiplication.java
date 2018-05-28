/*
 * xbabka01 xbobci00
 * blok ktory vynasobi int vstupy a da vysledok na vystup
 */
package ija_proj.scheme.Items.Integer;

import ija_proj.scheme.Items.AbstractItem;

import ija_proj.scheme.Items.val.IValue;
import ija_proj.scheme.Items.val.IValueInt;

import java.util.HashMap;
import java.util.Map;

import static java.lang.Double.NaN;

public class ItemIntegerMultiplication extends AbstractItem {
    public static final String Description =
            "x * y = [int]\n    porty:\n          [x -> int]\n          [y -> int]";

    public ItemIntegerMultiplication() {
        super("x * y");
        output = new IValueInt();
        Map<String, IValue> inport = new HashMap<>();
        inport.put("x", new IValueInt(NaN));
        inport.put("y", new IValueInt(NaN));
        this.setInput(inport);
        IValue out = new IValueInt(NaN);
        this.setOutput(out);
    }

    @Override
    public String getDescription() {
        return ItemIntegerMultiplication.Description;
    }


    @Override
    public void Execute()  throws Exception{
        IValueInt temp = (IValueInt) this.getInput("x");
        double val0 = temp.getValue();

        temp = (IValueInt) this.getInput("y");
        double val1 = temp.getValue();


        double result = NaN;
        if (!Double.isNaN(val0) && !Double.isNaN(val1)) {
            result = val0 * val1;
        }

        this.setOutput(new IValueInt(result));
    }
    protected void setOutput(IValue output){
        IValueInt out = (IValueInt) output;
        this.getOutput().setValue(out.toString());
    }
}
