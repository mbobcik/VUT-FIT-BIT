package ija_proj.scheme.Items;

import ija_proj.scheme.Items.Bool.ItemBoolAnd;
import ija_proj.scheme.Items.Bool.ItemBoolNot;
import ija_proj.scheme.Items.Bool.ItemBoolOr;
import ija_proj.scheme.Items.Bool.ItemBoolXor;
import ija_proj.scheme.Items.Convertor.*;
import ija_proj.scheme.Items.Double.*;
import ija_proj.scheme.Items.Integer.*;

import java.util.*;

public class ItemFactory {

    @FunctionalInterface
    public static interface genInterface{
        Item generator();
    }




    public static Map<String, genInterface> gen_constructors(){
        Map<String, genInterface> result = new HashMap<>();

        //key -> nazov ktory sa ma dat do button
        //value -> generator ktory generuje dany blok

        result.put("ADD[double]", ItemDoubleAddition::new);
        result.put("SUB[double]", ItemDoubleSubstraction::new);
        result.put("MUL[double]", ItemDoubleMultiplication::new);
        result.put("DIV[double]", ItemDoubleDivision::new);
        result.put("MOD[double]", ItemDoubleModulo::new);

        result.put("ADD[int]", ItemIntegerAddition::new);
        result.put("SUB[int]", ItemIntegerSubtraction::new);
        result.put("MUL[int]", ItemIntegerMultiplication::new);
        result.put("DIV[int]", ItemIntegerDivision::new);
        result.put("MOD[int]", ItemIntegerModulo::new);

        result.put("AND[bool]", ItemBoolAnd::new);
        result.put("OR[bool]", ItemBoolOr::new);
        result.put("XOR[bool]", ItemBoolXor::new);
        result.put("NOT[bool]", ItemBoolNot::new);

        result.put("D2I", ItemDoubleToInt::new);
        result.put("I2D", ItemIntToDouble::new);
        result.put("D2B", ItemDoubleToBool::new);
        result.put("I2B", ItemIntToBool::new);
        result.put("B2I", ItemBoolToInt::new);
        result.put("B2D", ItemBoolToDouble::new);

        return  result;
    }


}
