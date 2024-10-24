#ifndef GRAPHMATCH_H
#define GRAPHMATCH_H
//#define GRAPH_MATCH_DEBUG 1
#include "headers/graph/graph.h"
#include "headers/math/rigidbody.h"
#include "headers/graph/graphclique.h"
#include "headers/graph/graphpair.h"
#include "headers/math/matrix.h"
namespace protspace
{

template<class T>class GraphMatch
{
private:
typedef Pair<T> PairT ;
    typedef Clique<T> CliqueT ;
    /**
     * @brief Product graph that takes two graphs, and produces a graph with similar properties
     */
     Graph mProdGraph;


    /**
     * @brief List of pairs
     */
    std::vector<PairT*> mPairList;


    /**
     * @brief List of results clique
     */
    std::vector<CliqueT*> cliquelist;


    /**
     * @brief Temporary array of array of vertex defining clique
     */
    std::vector<std::vector<Vertex*> > mTmpresultlist;


    //std::vector<std::vector<bool>> mLevelCand;
    Matrix<bool> mLevelCand;

    /**
     * @brief TRUE when enumeration all cliques, including cliques found in cliques
     */
    bool mListAllClique;


    bool mFullScan;

    ///
    /// \brief List of vertex ordered by decreasing degree value
    ///
    std::vector<size_t> mOrder;


    /**
     * @brief Iininal number of pairs to save a clique
     */
    size_t mMinSizeClique;

    size_t mNPair;

    bool mMaxCliqueAllowedReached;

    static const size_t mMaxCliqueAllowed;


    void convertToClique();

/**
     * @brief Recursive function to get the next element in a clique
     * @param candidates List of potential pair that can be added to the current clique
     * @param clique Current clique
     * @param level For debug purpose
     */
    void seekNextCliqueElement(std::vector<Vertex *> clique
                               , const size_t &level);

     void getOrder();

     bool checkSize(const std::vector<bool>& candidates,
                    size_t currSize)const;
void addClique(std::vector<Vertex *>& newClique);
bool checkOverlap(const std::vector<Vertex*>& vl,
                                            const Clique<T> cl)const;
     size_t fillCandidacy(const size_t &level,
             const size_t& curr_pos);

     size_t getCandidates(const size_t &curr_i,
             const Vertex& curr_look,
             const size_t &level)const;

    /**
     * @brief Generates all sub-cliques that are within a clique
     * @param full_list Complete list of vertex of a given clique
     * @param currSel Current list of sub-clique of the full_list
     * @param endlist All sub clique and maximal clique will be stored in this list
     * @param last Current looked pair
     */
    void getNext(const std::vector<Vertex*>& full_list,
                 std::vector<Vertex*> currSel,
                 std::vector<std::vector<Vertex*> >& endlist,
                 const size_t& last);
    size_t getUniqRefCand(const size_t& level)const;
public:

    /**
     * @brief Standard constructor
     */
    GraphMatch();



    ~GraphMatch();


    /**
      * @brief Defines a new pair between two objects
      * @param obj1 Object in the reference
      * @param obj2 Object in the comparison
      * @return A pair that make the match between theses two objects
      * @throw 030201 Bad allocation
      */
     PairT &addPair(T& obj1, T& obj2) throw(ProtExcept);



     /**
     * @brief Add a link between two pairs
     * @param pair First pair to be considered
     * @param pair2 Second pair to be considered
     * @throw 030301 vertex1 is not part of this graph
     * @throw 030302 vertex2 is not part of this graph
     * @throw 030303 Bad allocation
     * @throw 020201 Given link not found in this dot
     */
    void addLink(PairT& pair, PairT& pair2) throw(ProtExcept);



    /**
     * @brief Add a link between two pairs and return the edge in the graph product
     * @param pair First pair to be considered
     * @param pair2 Second pair to be considered
     * @return Edge describing the potential match between these two pairs.
     * @throw 030301 vertex1 is not part of this graph
     * @throw 030302 vertex2 is not part of this graph
     * @throw 030303 Bad allocation
     * @throw 020201 Given link not found in this dot
     */
    Edge&  addLinkwEdge(Pair<T>& pair, Pair<T>& pair2)  throw(ProtExcept);



    /**
     * @brief Return the link between two pair as an edge in the product graph
     * @param Position of the link in the list of edges
     * @return Cooresponding edge
     * @throw 030901 Given position is above the number of edges
          */
    const Edge& getLink(const size_t& i)const throw(ProtExcept);



    /**
     * @brief Detect all cliques
     * @throw 030401 Given position is above the number of vertices

     */
    void calcCliques();



    /**
     * @brief True when you want to save all cliques, even cliques found within a clique
     * @param saveall True to consider all cliques
     */
    void saveAllClique(const bool& saveall){mListAllClique=saveall;}



    /**
     * @brief Remove all pairs and cliques
     */
    void clear();



    void clearEdges();
    /**
     * @brief Set The minimal size of the clique (Default 3)
     * @param minSize Minimal size of the clique to set to
     */
    void setMinSize(const size_t& minSize){mMinSizeClique=minSize;}



    /**
     * @brief Return the number of pairs
     * @return Number of pairs
     */
    size_t numPairs()const {return mPairList.size();}



    /**
     * @brief Return the number of edges between pairs in the product graph
     * @return Number of edges
     */
    size_t numEdges()const {return mProdGraph.numEdges();}



    /**
     * @brief Return the pair at a given position (nPair) of the list of pairs
     * @param nPair position in the list of pairs
     * @return Corresponding pair
     * @throw 140101 Given number is above the number of pairs
     */
    Pair<T> &getPair(const size_t& nPair);



    /**
     * @brief Get the clique at the nClique position in the results list
     * @param nClique Position of the clique in the results list
     * @return Clique at the nClique position
     * @throw 140201  Given number is above the number of clique
     */
    Clique<T>& getClique(const size_t& nClique);

    const Clique<T>& getClique(const size_t& nClique)const;

    /**
     * @brief Number of cliques found
     * @return Number of cliques found
     */
    size_t numCliques()const { return cliquelist.size();}



    /**
     * @brief Returns the generated product graph
     * @return
     */
    const Graph& getProdGraph() const {return mProdGraph;}



    void setFullScan(const bool& scan){mFullScan=scan;}


    void getGammaCliques(const double& gamma);


};
class MMAtom;
typedef Clique<MMAtom> AtomClique;
typedef Pair<MMAtom> AtomPair;
}
#include "graphmatch.tpp"

#endif // GRAPHMATCH_H

