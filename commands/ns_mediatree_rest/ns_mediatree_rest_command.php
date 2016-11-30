<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_INC . 'inc/search.php');
require_once(DOKU_INC . 'inc/pageutils.php');
require_once(DOKU_INC . 'inc/JSON.php');
require_once(DOKU_COMMAND . 'commands/ns_tree_rest/ns_tree_rest_command.php');

/**
 * Class ns_mediatree_rest_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class ns_mediatree_rest_command extends ns_tree_rest_command {
    /**
     * Obté l'arbre a partir del node actual ordenant
     * els resultats i excloent els directoris segons els valors dels paràmetres emmagatzemats en aquest objecte.
     *
     * @param string[] $extra_url_params paràmetres passats a travès de la URL.
     *
     * @return string arbre formatat com a JSON
     */
    public function processGet() {
//        global $conf;
//        $sortOptions=array(0 => 'name', 'date');
//        $tree = array();
//        $tree_json=  array();
        $strData; // TODO[Xavi] Error, no s'ha assignat cap valor.
        $json = new JSON();

        $tree = $this->modelWrapper->getNsMediaTree(
                                   $this->params['currentnode'],
                                   $this->params['sortBy'],
                                   $this->params['onlyDirs'],
                                   $this->params['hiddenProjects']
        );

        $strData = $json->enc($tree);
        return $strData;

    }
    
     /**
     * @return string Nnom de l'autorització a fer servir
     */
    public function getAuthorizationType() {
        return "_none";
    }
}