package ija_proj;

import ija_proj.scheme.MNode;

import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;

/**
 * klasa ktora parsuje a vie spustat MNode v spravnom poradi
 */
public class Executor {
    private List<MNode> roots;
    private List<MNode> all;
    private List<MNode> childs;
    private List<MNode> order = null;
    private Iterator<MNode> iter = null;
    private String log = null;

    public Executor(List<MNode> all) {
        this.all = all;
        this.childs = null;
        this.log = "";
    }

    /**
     * najde root z vsetkych a zisti ci sa nachadzaju kruhove vazby vo scheme
     *
     * @return ak sa podarilo tak true inak false
     */
    private boolean parse() {
        this.roots = new ArrayList<>(this.all);
        this.childs = new ArrayList<>();
        for (MNode node : this.all) {
            if (childs.contains(node)) {
                this.roots.clear();
                System.out.println("ERR CYKLUS");
                log = "ERR CYKLUS";
                return false;
            }

            for (String key : node.getInputs().keySet()) {
                MNode child = node.getInputs().get(key);
                if (child == node) {
                    this.roots.clear();
                    System.out.println("ERR CYKLUS");
                    log = "ERR CYKLUS";
                    return false;
                }
                if (child != null) {
                    childs.add(child);
                    roots.remove(child);
                }
            }
//            childs.add(node.getId());
//            for (String key :
//                    node.getInputs().keySet()) {
//                MNode child = node.getInputs().get(key);
//                if (child == node){
//                    this.roots.clear();
//                    System.out.println("ERR CYKLUS");
//                    log = "ERR CYKLUS";
//                    return false;
//                }
//                if (child != null){
//                    roots.remove(node.getInputs().get(key));
//                }
//            }
        }
        return all.size() == 0 || roots.size() > 0 ;
    }

    /**
     * vytvory poradie v akom sa ma schema vykonat
     *
     * @param order
     * @param root
     */
    private static void parseToList(List<MNode> order, MNode root) {
        for (String node :
                root.getInputs().keySet()) {
            if (root.getInputs().get(node) != null) {
                parseToList(order, root.getInputs().get(node));
            }
        }
        order.add(root);
    }

    /**
     * nainicialzuje triedu pred spustenym proframu
     * musi byt pred tym parse
     */
    public void init() {
        this.order = new ArrayList<>();
        for (MNode node :
                this.roots) {
            parseToList(this.order, node);
        }
        iter = order.iterator();
    }

    /**
     * parsuj a inicializuj a potom ak nemas krokovat a aj spusti
     *
     * @param steping
     * @return ak sa podarilo tak true inak false
     */
    public boolean start(boolean steping) {
        boolean par_res = parse();
        if (!par_res) {
            return par_res;
        }
        init();
        if (!steping) {
            for (MNode node :
                    this.order) {
                try {
                    node.execute();
                } catch (Exception e) {
//                    log = e.toString();
                    log = "CHYBA PRI VYKOVANI";

                    return false;
                }
            }
        }
        return true;
    }

    /**
     * ak je este nevykonany prikaz tak ho vykona inak nevykona nic
     */
    public boolean next() {
        if (iter.hasNext()) {
            MNode node = this.iter.next();
            try {
                node.execute();
            } catch (Exception e) {
//                log = e.toString();
                log = "CHYBA PRI VYKOVANI";
                return false;
            }
            return true;
        }
        return false;
    }


//    public List<MNode> getRoots() {
//        return roots;
//    }
}
