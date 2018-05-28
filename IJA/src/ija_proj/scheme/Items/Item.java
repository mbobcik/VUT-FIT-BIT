/*
 * xbabka01 xbobci00
 * sluzi ako vymenovanie prikazou ktorym ma blok rozumiet
 */
package ija_proj.scheme.Items;


import ija_proj.scheme.Items.val.IValue;

import java.util.Map;

public interface Item {
    String getName();

    void setName(String name);

    String getDescription();


    IValue getOutput();


    IValue getInput(String name);

    Map<String, IValue> getInput();

    void setInput(String name, IValue value);

    int getCountInput();

    void Execute() throws Exception;

}
