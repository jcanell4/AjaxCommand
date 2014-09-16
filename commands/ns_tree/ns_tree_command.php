<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_INC . 'inc/search.php');
require_once(DOKU_INC . 'inc/pageutils.php');
require_once(DOKU_INC . 'inc/JSON.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

/**
 * Class ns_tree_command
 * TODO[Xavi] Error, falta per implementar el mètode getDefaultResponse()
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class ns_tree_command extends abstract_command_class {
    /**
     * Estableix els valors de la propietat types del objecte i els valors per defecte.
     *
     * Els parametres establerts al objecte types son:
     *      currentnode: objecte (default = null)
     *      sortBy: enter (default = 0, valors possibles 0 per name, i 1 per date)
     *      explore: enter (default = 1)
     *      open: enter (default = 1)
     *      refreshAll: boolean (default = false)
     */
    public function __construct() {
        $this->types['currentnode'] = abstract_command_class::T_OBJECT; // TODO[Xavi] En aquest cas el que es fa servir a process() es un array associatiu i no pas un objecte, es correcte?
        $this->types['sortBy']      = abstract_command_class::T_INTEGER;
        $this->types['explore']     = abstract_command_class::T_INTEGER;
        $this->types['open']        = abstract_command_class::T_INTEGER;
        $this->types['refreshAll']  = abstract_command_class::T_BOOLEAN;

        $defaultValues = array(
            'sortBy'     => 0,
            'explore'    => 1,
            'open'       => 1,
            'refreshAll' => FALSE
        );

        $this->setParameters($defaultValues);
    }

    //put your code here
    /**
     * Genera el arbre de fitxers a partir del node actual si existeix, tenint en compte les opcions espcificades als
     * paràmetres.
     *
     * @return string arbre de fitxers en format JSON.
     */
    protected function process() {
        $sortOptions = array(0 => 'name', 1 => 'date'); // TODO[Xavi] No cal especificar l'index, el primer element es el 0 i el següent el 1
        global $conf;
        $tree = array(); //TODO[Xavi] No es fa servir aquest valor, no cal declarar-lo
        $strData; // TODO[Xavi] Error, no s'ha assignat cap valor, no cal declarar la variable
        $json = new JSON();

        if($this->params['currentnode']) {
            $node = $this->params['currentnode'];
        } else {
            $node = array('id' => '', 'level' => 0, 'open' => TRUE, 'type' => 'd');
        }
        $sort = $sortOptions[$this->params['sortBy']];
        $base = $conf['datadir'];

        $maxLevel = $node['level'] + $this->params['explore'];

        $tree = $this->generateFileTree($node, $maxLevel, $base, $sort);

        $strData = $json->enc($tree);
        return $strData;
    }

//    protected function generateFileTree($root, $maxLevel, $base, $sort=false){
//        
//        search(&$data,$base,$func,$opts,$dir='',$lvl=1,$sort=false);
//    }

    /**
     * Retorna el arbre de fitxers a partir de l'arrel especificada amb el nombre de nivells passats com argument.
     *
     * @param array   $root     array associatiu amb la informació arrel del arbre
     * @param integer $maxLevel nombre de nivells a recorre com a màxim
     * @param string  $base     url base del arbre
     * @param bool    $sort     cert per retornar-lo ordenat o fals en cas contrari.
     *
     * @return object
     */
    protected function generateFileTree($root, $maxLevel, $base, $sort = FALSE) {
        $nodeQueue = array();
//        array_push($nodeQueue, $root);
        $nodeQueue[] = & $root;
//        $currentLevel = $root['level'];
        while(count($nodeQueue) > 0) {
//            $node = array_pop($nodeQueue);
            if(isset($node)) {
                unset($node);
            }
            reset($nodeQueue);
            $key  = key($nodeQueue);
            $node = & $nodeQueue[$key];
            unset($nodeQueue[$key]);

            $data = array();
            $opts = array('ns' => $node['id']);
            $dir  = str_replace(':', '/', $node['id']);
            search(
                $data, $base, 'ns_tree_command::setInfo',
                $opts, $dir, $node['level'] + 1, $sort
            );
            if($node['type'] === 'd' && count($data) > 0) {
                $node['children'] = array();
            }
            foreach($data as $value) {
                $node['children'][] =& $value;
                $currentLevel       = $value['level'];
                if($this->params['open'] < $currentLevel) {
                    $value['open'] = FALSE;
                }
                if($value['type'] == 'd' && $currentLevel < $maxLevel) {
//                    array_push($nodeQueue, $value);
                    $nodeQueue[] = & $value;
                }
                unset($value);
            }
        }
        return $root;
    }

    /**
     * Crida al mètode search_index de DokuWiki(search.php) que genera l'index de pàgines amb els arguments passats.
     * // TODO[Xavi] No trobo on es crida, i no es retorna el valor obtingut, funciona correctament?
     */
    public function setInfo(&$data, $base, $file, $type, $lvl, $opts) {
        search_index($data, $base, $file, $type, $lvl, $opts);
    }

    protected function preprocess() {

    }

    protected function startCommand() {

    }
}