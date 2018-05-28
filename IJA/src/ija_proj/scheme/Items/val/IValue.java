package ija_proj.scheme.Items.val;

import java.util.Objects;
import java.util.Observable;

/**
 * Abstraktna trieda ktora sluzi ako uloziste pre hodnoty (podtriedu) a typ
 * typ je ulozeny ako string
 */
public abstract class IValue extends Observable implements  java.io.Serializable{
    private final String type;

    /**
     * Vytvori novu IValue s typom type
     * @param type typ ktoreho je
     */
    public IValue(String type) {
        this.type = type;
    }

    /**
     * Vratio typ
     * @return type
     */
    public String getType() {
        return type;
    }

    /**
     * skopiruje honotu z val do seba
     * nekopiruje hodnoru
     * treba dbat ze je rovnakeho typu ak nie tak neco
     * @param val hodnota ktora sa skopiruje
     */
    public abstract void copyVal(IValue val);

    @Override
    public boolean equals(Object o) {
        if (this == o) return true;
        if (!(o instanceof IValue)) return false;
        IValue iValue = (IValue) o;
        return Objects.equals(getType(), iValue.getType());
    }

    @Override
    public int hashCode() {

        return Objects.hash(getType());
    }

    /**
     * Nastavy hodnotu zapisanu v retazci
     * @param value retazec ktory nesie hodnotu
     */
    public abstract void setValue(String value);

    /**
     * Premeni hodnotu na retazec
     * @return retazec zodnpovedajuci hodnote
     */
    public abstract String getStringValue();
}
