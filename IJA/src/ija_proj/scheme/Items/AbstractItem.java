/*
 * xbabka01 xbobci00
 * abstrakcna trieda ktora sluzi ako forma pre vytvaranie blokou
 */
package ija_proj.scheme.Items;

import ija_proj.scheme.Items.val.IValue;

import java.util.Map;

/**
 * @author peter
 */
public abstract class AbstractItem implements Item, java.io.Serializable {
    private String Name;
    protected IValue output; //prerobit na rozne typy
    Map<String, IValue> input;


    public AbstractItem(String name) {
        Name = name;
        this.output = null;
        this.input = null;

    }


    @Override
    public String getName() {
        return this.Name;
    }

    @Override
    public void setName(String name) {
        this.Name = name;
    }


    @Override
    public IValue getOutput() {
        return this.output;
    }



    protected abstract void setOutput(IValue output);
    /* {

        if (this.output == null || this.output.getType().equals(output.getType())) {
            this.output = output;
        } else {
            //todo
        }
    }*/

    @Override
    public IValue getInput(String name) {
        return this.input.get(name);
    }

    @Override
    public void setInput(String name, IValue value) {
        IValue myval = this.input.get(name);
        if (myval != null) {
            myval.copyVal(value);
            this.input.put(name, myval);
        } else {
            //todo
        }
    }

    protected void setInput(Map<String, IValue> input) {
        this.input = input;
    }

    @Override
    public int getCountInput() {
        return this.input.size();
    }

    public Map<String, IValue> getInput() {
        return this.input;
    }
}
