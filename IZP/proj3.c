/**
 * @name 3. project
 * @author Martin Bobcik xbobci00
 * @date 13.12.2015
 * @note Project is not complete.
 * @note Program is not working.
 * @version v0.1 pre-alpha
 */

/**
 * Kostra programu pro 3. projekt IZP 2015/16
 *
 * Jednoducha shlukova analyza: 2D nejblizsi soused.
 * Single linkage
 * http://is.muni.cz/th/172767/fi_b/5739129/web/web/slsrov.html
 */
#include <stdio.h>
#include <stdlib.h>
#include <assert.h>
#include <math.h> // sqrtf
#include <limits.h> // INT_MAX

/*****************************************************************
 * Ladici makra. Vypnout jejich efekt lze definici makra
 * NDEBUG, napr.:
 *   a) pri prekladu argumentem prekladaci -DNDEBUG
 *   b) v souboru (na radek pred #include <assert.h>
 *      #define NDEBUG
 */
#ifdef NDEBUG
#define debug(s)
#define dfmt(s, ...)
#define dint(i)
#define dfloat(f)
#else

// vypise ladici retezec
#define debug(s) printf("- %s\n", s)

// vypise formatovany ladici vystup - pouziti podobne jako printf
#define dfmt(s, ...) printf(" - "__FILE__":%u: "s"\n",__LINE__,__VA_ARGS__)

// vypise ladici informaci o promenne - pouziti dint(identifikator_promenne)
#define dint(i) printf(" - " __FILE__ ":%u: " #i " = %d\n", __LINE__, i)

// vypise ladici informaci o promenne typu float - pouziti
// dfloat(identifikator_promenne)
#define dfloat(f) printf(" - " __FILE__ ":%u: " #f " = %g\n", __LINE__, f)

#endif

/*****************************************************************
 * Deklarace potrebnych datovych typu:
 *
 * TYTO DEKLARACE NEMENTE
 *
 *   struct obj_t - struktura objektu: identifikator a souradnice
 *   struct cluster_t - shluk objektu:
 *      pocet objektu ve shluku,
 *      kapacita shluku (pocet objektu, pro ktere je rezervovano
 *          misto v poli),
 *      ukazatel na pole shluku.
 */

struct obj_t
{
    int id;
    float x;
    float y;
};

struct cluster_t
{
    int size;
    int capacity;
    struct obj_t *obj;
};

/*****************************************************************
 * Deklarace potrebnych funkci.
 *
 * PROTOTYPY FUNKCI NEMENTE
 *
 * IMPLEMENTUJTE POUZE FUNKCE NA MISTECH OZNACENYCH 'TODO'
 *
 */

/*
 Inicializace shluku 'c'. Alokuje pamet pro cap objektu (kapacitu).
 Ukazatel NULL u pole objektu znamena kapacitu 0.
*/
/**
* This method initialize cluster. It allocate memory for "capacity" of
* objects, and assign values of capacity and size of cluster.
* @param c - cluster, which will be initialized
* @param cap - capacity of the cluster. Count od object, which can cluster hold.
*/
void init_cluster(struct cluster_t *c, int cap)
{
    assert(c != NULL);
    assert(cap >= 0);

    // TODO
    //c = malloc(sizeof(struct cluster_t));
    c->obj = malloc(cap * sizeof(struct obj_t)); // error
    c->capacity = cap;
    c->size = 0;
}

/*
 Odstraneni vsech objektu shluku a inicializace na prazdny shluk.
 */
 /**
 * This method remove all objects of the cluster from memory and
 * initialize new, empty cluster with capacity of the previous one
 * @param {c - cluster, which objects will be removed, and which will be
 *            reinitialized to empty cluster }
 */
void clear_cluster(struct cluster_t *c)
{
    //TODO
    int i;
    for(i = 0; i < c ->size; i++)
    {
        free(c->obj + i);
    }
    init_cluster(c, c->capacity);
}

/// Chunk of cluster objects. Value recommended for reallocation.
const int CLUSTER_CHUNK = 10;

/*
 Zmena kapacity shluku 'c' na kapacitu 'new_cap'.
 */
struct cluster_t *resize_cluster(struct cluster_t *c, int new_cap)
{
    // TUTO FUNKCI NEMENTE
    assert(c);
    assert(c->capacity >= 0);
    assert(new_cap >= 0);

    if (c->capacity >= new_cap)
        return c;

    size_t size = sizeof(struct obj_t) * new_cap;

    void *arr = realloc(c->obj, size);
    if (arr == NULL)
        return NULL;

    c->obj = arr;
    c->capacity = new_cap;
    return c;
}

/*
 Prida objekt 'obj' na konec shluku 'c'. Rozsiri shluk, pokud se do nej objekt
 nevejde.
 */
 /**
 *  This method append (add on the end) object to cluster, and increment cluster size.
 *  If the cluster is full, the method will resize the cluster to twice the capacity,
 *  and then append the object.
 *  @param c - cluster, to which will be the object obj appended
 *  @param obj - object which will be appended to cluster c
 */
void append_cluster(struct cluster_t *c, struct obj_t obj)
{
    if(c->size + 1 <= c->capacity)
    {
        c->obj[c->size + 1] = obj;

        c->size += 1;
    }
    else
    {
        c = resize_cluster(c, c->capacity*2);
        c->obj[c->size + 1]= obj;
        c->size += 1;
    }

}

/*
 Seradi objekty ve shluku 'c' vzestupne podle jejich identifikacniho cisla.
 */
void sort_cluster(struct cluster_t *c);

/*
 Do shluku 'c1' prida objekty 'c2'. Shluk 'c1' bude v pripade nutnosti rozsiren.
 Objekty ve shluku 'c1' budou serazny vzestupne podle identifikacniho cisla.
 Shluk 'c2' bude nezmenen.
 */
void merge_clusters(struct cluster_t *c1, struct cluster_t *c2)
{
    assert(c1 != NULL);
    assert(c2 != NULL);

    // TODO
}

/**********************************************************************/
/* Prace s polem shluku */

/*
 Odstrani shluk z pole shluku 'carr'. Pole shluku obsahuje 'narr' polozek
 (shluku). Shluk pro odstraneni se nachazi na indexu 'idx'. Funkce vraci novy
 pocet shluku v poli.
*/
int remove_cluster(struct cluster_t *carr, int narr, int idx)
{
    assert(idx < narr);
    assert(narr > 0);

    // TODO

    return 1;
}

/*
 Pocita Euklidovskou vzdalenost mezi dvema objekty.
 */
 /**
 * This method measures the Euclidean distance od two objects on input.
 * Euclidean distance is a distance between two points in Euclidean space.
 * Euclidean distance can be measured by square root of sum of
 * position of first object, deducted by position of second object, squared
 * @param *o1 - first object of type struct obj_t *
 * @param *o2 - second object of type struct obj_t *
 * @return distance between two input objects
 */
float obj_distance(struct obj_t *o1, struct obj_t *o2)
{
    assert(o1 != NULL);
    assert(o2 != NULL);

    // TODO
    float x1 = o1 ->x;
    float y1 = o1 ->y;
    float x2 = o2 ->x;
    float y2 = o2 ->y;

    float result = sqrtf((x1 - y1) * (x1 - y1) + (x2 - y2) * (x2 - y2));
    return result;
}

/*
 Pocita vzdalenost dvou shluku. Vzdalenost je vypoctena na zaklade nejblizsiho
 souseda.
*/
float cluster_distance(struct cluster_t *c1, struct cluster_t *c2)
{
    assert(c1 != NULL);
    assert(c1->size > 0);
    assert(c2 != NULL);
    assert(c2->size > 0);

    // TODO

    return 1.0;
}

/*
 Funkce najde dva nejblizsi shluky. V poli shluku 'carr' o velikosti 'narr'
 hleda dva nejblizsi shluky (podle nejblizsiho souseda). Nalezene shluky
 identifikuje jejich indexy v poli 'carr'. Funkce nalezene shluky (indexy do
 pole 'carr') uklada do pameti na adresu 'c1' resp. 'c2'.
*/
void find_neighbours(struct cluster_t *carr, int narr, int *c1, int *c2)
{
    assert(narr > 0);

    // TODO
}

// pomocna funkce pro razeni shluku
static int obj_sort_compar(const void *a, const void *b)
{
    // TUTO FUNKCI NEMENTE
    const struct obj_t *o1 = a;
    const struct obj_t *o2 = b;
    if (o1->id < o2->id) return -1;
    if (o1->id > o2->id) return 1;
    return 0;
}

/*
 Razeni objektu ve shluku vzestupne podle jejich identifikatoru.
*/
void sort_cluster(struct cluster_t *c)
{
    // TUTO FUNKCI NEMENTE
    qsort(c->obj, c->size, sizeof(struct obj_t), &obj_sort_compar);
}

/*
 Tisk shluku 'c' na stdout.
*/
void print_cluster(struct cluster_t *c)
{
    // TUTO FUNKCI NEMENTE
    int size = c ->size;
    int i;
    for (i = 0; i < size; i++)
    {
        if (i) putchar(' ');
        printf("%d[%g,%g]", c->obj[i].id, c->obj[i].x, c->obj[i].y);
    }
    putchar('\n');
}

/*
 Ze souboru 'filename' nacte objekty. Pro kazdy objekt vytvori shluk a ulozi
 jej do pole shluku. Alokuje prostor pro pole vsech shluku a ukazatel na prvni
 polozku pole (ukalazatel na prvni shluk v alokovanem poli) ulozi do pameti,
 kam se odkazuje parametr 'arr'. Funkce vraci pocet nactenych objektu (shluku).
 V pripade nejake chyby uklada do pameti, kam se odkazuje 'arr', hodnotu NULL.
*/
/**
*   This method open the file, load objects from file, initialize separate cluster
*   for each object in array of clusters, and add the object to the cluster.
*   After loading, the method returns pointer on array of clusters, and intereger
*   number of loaded objects.
*   This method does not work!
*   @param filename - string value, which contains path to the txt file with formated objects
*   @param **arr - pointer to array of clusters
*   @return returns count of loaded objects
*/
int load_clusters(char *filename, struct cluster_t **arr)
{
    assert(arr != NULL);

    // TODO
    FILE *fclus = fopen(filename,"r");

    int countOfChars = 6; //count of chars, until count of objects in file("count=20")
    int i;
    for(i = 0; i<countOfChars; i++)
    {
        fgetc((FILE*)fclus);
    }
    int countOfObjects = 0;
    fscanf((FILE*)fclus,"%d",&countOfObjects); // scans for number of objects in file
    i = 0;
    printf("countOfChars>%d\n",countOfObjects);
    struct cluster_t *arrayCluster = malloc(sizeof(struct cluster_t) * countOfObjects);
    for(; i < countOfObjects; i++)
    {
        struct cluster_t *newCluster = &arrayCluster[i];
        init_cluster(newCluster, 1);
        // scans objects in file until EOF
        int objid, objx, objy;
        fscanf(fclus," %d", &objid);
        fscanf(fclus," %d", &objx);
        fscanf(fclus," %d[^\n]", &objy );
        //initialize obj_t struct
        struct obj_t inputObject;
        inputObject.id = objid;
        inputObject.x = objx;
        inputObject.y = objy;


        append_cluster(newCluster,inputObject); // append obj in cluster
        print_cluster(newCluster);
    }
    *arr = arrayCluster;
    fclose(fclus);

    return i;
}

/*
 Tisk pole shluku. Parametr 'carr' je ukazatel na prvni polozku (shluk).
 Tiskne se prvnich 'narr' shluku.
*/
void print_clusters(struct cluster_t *carr, int narr)
{
    printf("Clusters:\n");
    int i;
    for (i = 0; i < narr; i++)
    {
        printf("cluster %d: ", i);
        print_cluster(&carr[i]);
    }
}

int main(int argc, char *argv[])
{
    struct cluster_t *clusters;
    if(argc >= 2 && argc <=3)
    {
        puts("1");
        load_clusters(argv[1],&clusters);
        puts("2");
        print_clusters(clusters, 20);
    }
    // TODO

    return 0;
}

