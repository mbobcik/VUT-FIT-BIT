/**
 * @name 3. project Single Linkage
 * @author Martin Bobcik xbobci00
 * @date 13.12.2015
 * @version v0.1
 */

/**
* @brief Structure object
* this structure represents object and it holds ID and coordinates of the object.
* It can be stored in cluster_t structure
*/
struct obj_t {
    int id;         ///< unique ID of the object
    float x;        ///< X coordinate of the object
    float y;        ///< Y coordinate of the object
};

/**
*   @brief Structure cluster
*   this structure represent cluster of objects obj_t.
*   It holds number of objects in intereger variable size,
*   capacity of the cluster and pointer to the first object in the cluster.
*   Capacity means for how many objects have cluster allocated memory.
*/
struct cluster_t {
    int size;           ///< size of the cluster
    int capacity;       ///< capacity of the cluster
    struct obj_t *obj;  ///< pointer to objects of the cluster
};

/**
* This method initialize cluster. It allocate memory for "capacity" of
* objects, and assign values of capacity and size of cluster.
* @param c - cluster, which will be initialized
* @param cap - capacity of the cluster. Count od object, which can cluster hold.
*/
void init_cluster(struct cluster_t *c, int cap);

/**
 * This method remove all objects of the cluster from memory and
 * initialize new, empty cluster with capacity of the previous one
 * @param c - cluster, which objects will be removed, and which will be
 *            reinitialized to empty cluster
 */
void clear_cluster(struct cluster_t *c);

extern const int CLUSTER_CHUNK;

struct cluster_t *resize_cluster(struct cluster_t *c, int new_cap);
/**
 *  This method append (add on the end) object to cluster, and increment cluster size.
 *  If the cluster is full, the method will resize the cluster to twice the capacity,
 *  and then append the object.
 *  @param c - cluster, to which will be the object obj appended
 *  @param obj - object which will be appended to cluster c
 */
void append_cluster(struct cluster_t *c, struct obj_t obj);

/**
*   @brief Merge two clusters
*   This method copy the second cluster on the end of the second cluster.
*   if the first cluster reaches its capacity, it will be resized until the second
*   cluster fits. objects in the first cluster will be sorter by their IDs.
*   @param c1 - first cluster, to which will be the second copyed, and which can be resized.
*   @param c2 - second cluster which will be copyed to the end of the first cluster, and stay untouched after
*   @pre clusters must be allocated and second cluster should contain at least one object.
*/
void merge_clusters(struct cluster_t *c1, struct cluster_t *c2);

/**
*   @brief Remove cluster from array of clusters
*   this method remove cluster on the index idx from the carr array of clusters
*   with narr number of clusters. The method returns new number of clusters
*   in array.
*   @param carr - array of clusters from which will be the specified cluster removed
*   @param narr - number of clusters int the array of clusters carr
*   @param idx - index of the cluster in array of clusters carr, which will be removed
*   @return new number of clusters in array of clusters
*   @pre index of cluster should be inside of the array and narr should be true number of clusters in array
*   @post cluster on idx index is removed, and clusters after this clusters are moved to new positions
*/
int remove_cluster(struct cluster_t *carr, int narr, int idx);

/**
 * This method measures the Euclidean distance od two objects on input.
 * Euclidean distance is a distance between two points in Euclidean space.
 * Euclidean distance can be measured by square root of sum of
 * position of first object, deducted by position of second object, squared
 * @param *o1 - first object of type struct obj_t *
 * @param *o2 - second object of type struct obj_t *
 * @return distance between two input objects
 * @pre both objects must have valid coordinates
 */
float obj_distance(struct obj_t *o1, struct obj_t *o2);

/**
* @brief Measures distance between two clusters
* this method measures distance between two clusters by measuring distance
* between two nearest objects in two clusters. It measures distance for every object
* in both clusters, and return the lowest one.
* @param c1 - first cluster
* @param c2 - second cluster
* @return distance between two clusters
*/
float cluster_distance(struct cluster_t *c1, struct cluster_t *c2);

/**
* @brief find two nearest clusters in array
* This method will find two nearest clusters in carr array. Carr have narr number of
* cluster. Method will find them bz measuring the nearest objects in clusters.
* two nearest clusters will bz saved to c1 and c2 pointer.
* @param carr -
* @param narr -
* @param c1 -
* @param c2 -
*/
void find_neighbours(struct cluster_t *carr, int narr, int *c1, int *c2);

/**
* @brief Sort all objects in cluster
* This method sorts all objects in the cluster c by their IDs.
* It uses quick sort method qsort from stdlib library.
* Best performance of quicksort method is O(n log n),
* while the worst is O(n^2), which is done by bad
* pivot positioning.
* @param c - cluster of objects to sort
*/
void sort_cluster(struct cluster_t *c);

/**
*   @brief Print all objects of the cluster
*   this method prints all objects of the cluster, one beside the other
*   and print new line ('\n').
*   Format: "id[x,y] "
*   @param c - cluster of objects to print
*/
void print_cluster(struct cluster_t *c);
/**
*   This method open the file, load objects from file, initialize separate cluster
*   for each object in array of clusters, and add the object to the cluster.
*   After loading, the method returns pointer on array of clusters, and intereger
*   number of loaded objects.
*   @param filename - string value, which contains path to the txt file with formated objects
*   @param **arr - pointer to array of clusters
*   @return returns count of loaded objects
*/
int load_clusters(char *filename, struct cluster_t **arr);

/**
* @brief Print clusters from array
* This method will print first narr clusters from
* the carr array of clusters. This method uses the print_cluster
* method. For more information view it.
* Method will print all clusters, if narr is greater than there are clusters in array
* @param carr - array of cluster to print
* @param narr - number of first clusters in array to print
*/
void print_clusters(struct cluster_t *carr, int narr);
