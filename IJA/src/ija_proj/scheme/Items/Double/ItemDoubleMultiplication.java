/*
 * xbabka01 xbobci00
 * blok ktory vynasobi vstupy a da vysledok na vystup
 */
package ija_proj.scheme.Items.Double;

import ija_proj.scheme.Items.AbstractItem;

import ija_proj.scheme.Items.val.IValue;
import ija_proj.scheme.Items.val.IValueDouble;

import java.util.HashMap;
import java.util.Map;

import static java.lang.Double.NaN;

public class ItemDoubleMultiplication extends AbstractItem {
    public static final String Description =
            "x * y = [double]\n    porty:\n          [x -> double]\n          [y -> double]";

    public ItemDoubleMultiplication() {
        super("x * y");
        output = new IValueDouble();
        Map<String, IValue> inport = new HashMap<>();
        inport.put("x", new IValueDouble(NaN));
        inport.put("y", new IValueDouble(NaN));
        this.setInput(inport);
        IValue out = new IValueDouble(NaN);
        this.setOutput(out);
    }

    @Override
    public String getDescription() {
        return ItemDoubleMultiplication.Description;
    }


    @Override
    public void Execute()  throws Exception{
        IValueDouble temp = (IValueDouble) this.getInput("x");
        double val0 = temp.getValue();

        temp = (IValueDouble) this.getInput("y");
        double val1 = temp.getValue();


        double result = NaN;
        if (!Double.isNaN(val0) && !Double.isNaN(val1)) {
            result = val0 * val1;
        }

        this.setOutput(new IValueDouble(result));
    }
    protected void setOutput(IValue output){
        IValueDouble out = (IValueDouble) output;
        this.getOutput().setValue(out.toString());
    }
}
