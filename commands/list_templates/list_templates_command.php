<?php
/**
 * Retorna un array en formato JSON que contiene la lista de plantillas de documentos
 *
 * @culpable Rafael Claver
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");

require_once(DOKU_INC . 'inc/search.php');
require_once(DOKU_INC . 'inc/pageutils.php');
require_once(DOKU_INC . 'inc/JSON.php');
require_once(DOKU_COMMAND . 'abstract_rest_command_class.php');

class list_templates_command extends abstract_rest_command_class {

    public function __construct() {
        parent::__construct();
        $defaultValues = array(
             'sortBy'   => 0
            ,'onlyDirs' => "TRUE"
            ,'expandProject' => "FALSE"
            ,'hiddenProjects' => "TRUE"
        );
        $this->setParameters($defaultValues);
    }

    public function init( $modelManager = NULL ) {
        parent::init($modelManager);
        $this->authenticatedUsersOnly = FALSE;
    }

    /**
     * Obté la llista de plantilles del fitxer de configuració
     * @return string llista en format JSON
     */
    public function processGet() {
        $tree = $this->getListTemplates();
        return $tree;
    }
    //esta función està aquí temporalemente
    private function getListTemplates() {
        include(DOKU_PLUGIN . 'wikiiocmodel/conf/default.php');
        return json_encode($conf['projects']['defaultProject']['templates']);
    }

    /**
     * Extreu els paràmetres de la url passada com argument i els estableix com a paràmetres del objecte.
     *
     * @param string[] $extra_url_params paràmetres per extreure
     */
    public function setParamValuesFromUrl($extra_url_params) {
        $this->setCurrentNode($extra_url_params);
        $this->setSortBy($extra_url_params);
        $this->setOnlyDirs($extra_url_params);
        $this->setExpandProject($extra_url_params);
        $this->setHiddenProjects($extra_url_params);
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
     * Extreu el valor de ordenació de @param i l'estableix com a valor del paràmetre 'sortBy'. 
     * Aquest valor es trobarà a l'index 1.
     *
     * @param string[] $extra_url_params
     */
    private function setSortBy($extra_url_params) {
        if ($extra_url_params[1])
            $this->params['sortBy'] = settype($extra_url_params[1], $this->types['sortBy']);
    }

    /**
     * Extreu el valor per establir si s'han de filtrar els directoris o no.
     * Aquest valor es trobarà a l'index 2.
     *
     * @param string[] $extra_url_params
     */
    private function setOnlyDirs($extra_url_params) {
        if ($extra_url_params[2])
            $this->params['onlyDirs'] = ($extra_url_params[2] != 'f' && $extra_url_params[2] != 'false');
    }

    /**
     * Extreu el valor 'expandProject'. Aquest valor es trobarà a l'index 3.
     */
    private function setExpandProject($extra_url_params) {
        if ($extra_url_params[3])
            $this->params['expandProject'] = ($extra_url_params[3] != 'f');
    }

    /**
     * Extreu el valor 'setHiddenProjects'. Aquest valor es trobarà a l'index 4.
     */
    private function setHiddenProjects($extra_url_params) {
        if ($extra_url_params[4])
            $this->params['hiddenProjects'] = ($extra_url_params[4] != 'f');
    }

    function getDefaultResponse( $response, &$ret ) {
        $ret->setEncodedResponse($response);
    }

    /**
     * @return string nom del 'command' corresponent a l'autorització què es vol fer servir
     */
    public function getAuthorizationType() {
        return "_none";
    }
}
