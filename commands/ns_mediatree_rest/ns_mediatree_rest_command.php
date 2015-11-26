<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
//if(!defined('CURRENT_NODE_NS_TREE_PARAM')) define('CURRENT_NODE_NS_TREE_PARAM', 1);
require_once(DOKU_INC . 'inc/search.php');
require_once(DOKU_INC . 'inc/pageutils.php');
require_once(DOKU_INC . 'inc/JSON.php');
require_once(DOKU_COMMAND . 'abstract_rest_command_class.php');

/**
 * Class ns_tree_rest_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class ns_mediatree_rest_command extends abstract_rest_command_class {

 
    
    /**
     * El constructor defineix el content type per defecte, els content type suportats, el mètode ('GET'), els tipus i
     * els valors per defecte sortBy = 0 i onlydirs = FALSE i els estableix com a paràmetres.
     */
    public function __construct() {
        parent::__construct();
        $this->defaultContentType    = "application/json";
        $this->supportedContentTypes = array("application/json");
        $this->supportedMethods      = array("GET");
        $this->types['currentnode']  = abstract_command_class::T_OBJECT;
        $this->types['sortBy']       = abstract_command_class::T_INTEGER;
        $this->types['onlyDirs']     = abstract_command_class::T_BOOLEAN;
        //$this->types['explore'] = abstract_command_class::T_INTEGER;
        //$this->types['open'] = abstract_command_class::T_INTEGER;
        //$this->types['refreshAll'] = abstract_command_class::T_BOOLEAN;

        $defaultValues = array(
            'sortBy'     => 0
            , 'onlyDirs' => FALSE
        );

        $this->setParameters($defaultValues);
    }

    /**
     * Extreu els paràmetres passats a travès de la URL si existeixen, i obté l'arbre a partir del node actual ordenant
     * els resultats i excloent els directoris segons els valors dels paràmetres emmagatzemats en aquest objecte.
     *
     * @param string[] $extra_url_params paràmetres passats a travès de la URL.
     *
     * @return string arbre formatat com a JSON
     */
    public function processGet($extra_url_params) {
//        global $conf;
//        $sortOptions=array(0 => 'name', 'date');
//        $tree = array();
//        $tree_json=  array();
        $strData; // TODO[Xavi] Error, no s'ha assignat cap valor.
        $json = new JSON();

        if(!is_null($extra_url_params)) {
            $this->setParamValuesFromUrl($extra_url_params);
        }

        $tree = $this->modelWrapper->getNsMediaTree(
                                   $this->params['currentnode'],
                                   $this->params['sortBy'],
                                   $this->params['onlyDirs']
        );

        $strData = $json->enc($tree);
        return $strData;

    }

//    protected function startCommand(){        
//    }
//    
//    protected function preprocess(){        
//    }

    /**
     * Extreu els paràmetres de la url passada com argument i els estableix com a paràmetres del objecte.
     *
     * @param string[] $extra_url_params paràmetres per extreure
     */
    private function setParamValuesFromUrl($extra_url_params) {
        $this->setCurrentNode($extra_url_params);
        $this->setOnlyDirs($extra_url_params);
        $this->setSortBy($extra_url_params);
    }

    /**
     * Extreu el node actual tenint en compte que sempre es l'ultim valor emmagatzemat a l'array i l'estableix com
     * a paràmetre 'currentnode'
     *
     * @param string[] $extra_url_params
     */
    private function setCurrentNode($extra_url_params) {
        $id                          = count($extra_url_params) - 1;
        $this->params['currentnode'] = $extra_url_params[$id];
    }

    /**
     * Extreu el valor de ordenació del array passat com argument i l'estableix com a paràmetre 'sortBy'. Aquest valor
     * es trobarà al index 2 sempre que la mida del array sigui superior a 3, si no es així la crida a aquest mètode no
     * te cap efecte.
     *
     * @param string[] $extra_url_params
     */
    private function setSortBy($extra_url_params) {
        if(count($extra_url_params) > 3) {
            $this->params['sortBy'] = $extra_url_params[2];
        }
    }

    /**
     * Extreu el valor per establir si s'han de filtrar els directoris o no i l'estableix com a valor del paràmetre
     * 'onlyDirs'.
     *
     * Si la mida del array es superior a 2 es comprova si al index 1 el valor emmagatzemat, si no es així la crida a
     * aquest mètode no te cap efecte. En cas de que el valor sigui 'f' o 'false' el valor establert serà fals, en cas
     * contrari serà cert.
     *
     * El valor establert es un booleà.
     *
     * @param string[] $extra_url_params
     */
    private function setOnlyDirs($extra_url_params) {
        if(count($extra_url_params) > 2) {
            $ret                      = ($extra_url_params[1] != 'f'
                && $extra_url_params[1] != 'false');
            $this->params['onlyDirs'] = $ret;
        }
    }

}