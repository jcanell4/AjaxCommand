<?php
if (!defined('DOKU_INC')) die();
require_once(DOKU_INC . 'inc/search.php');
require_once(DOKU_INC . 'inc/pageutils.php');
//require_once(DOKU_INC . 'inc/JSON.php');

/**
 * Class ns_tree_rest_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class ns_tree_rest_command extends abstract_rest_command_class {

    public function __construct() {
        parent::__construct();
        $this->types['currentnode']   = self::T_OBJECT;
        $this->types['onlyDirs']      = self::T_BOOLEAN;
        $this->types['sortBy']        = self::T_INTEGER;
        $this->types['expandProject'] = self::T_BOOLEAN;
        $this->types['hiddenProjects']= self::T_BOOLEAN;
        $defaultValues = array(
             'sortBy'   => 0
            ,'onlyDirs' => FALSE
            ,'expandProject' => FALSE
            ,'hiddenProjects' => FALSE
        );
        $this->setParameters($defaultValues);
        $this->throwsEventResponse=FALSE;
    }

    /**
     * El constructor defineix el content type per defecte, els content type suportats, el mètode ('GET'), els tipus i
     * els valors per defecte sortBy = 0 i onlydirs = FALSE i els estableix com a paràmetres.
     */
    public function init($modelManager = NULL) {
        parent::init($modelManager);
        $this->authenticatedUsersOnly = FALSE;
    }

    /**
     * Obté l'arbre a partir del node actual ordenant els resultats i excloent
     * els directoris segons els valors dels paràmetres emmagatzemats en aquest objecte.
     *
     * @param string[] $extra_url_params paràmetres passats a travès de la URL.
     * @return string arbre formatat com a JSON
     */
    public function processGet() {
        $action = $this->getModelManager()->getActionInstance("NsTreeAction");
        $contentData = $action->get($this->params);
        return $contentData;
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
        $this->setFromRoot($extra_url_params);
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
     * Extreu el valor 'setFromRoot'. Aquest valor es trobarà a l'index 5.
     */
    private function setFromRoot($extra_url_params) {
        if (count($extra_url_params)>6)
            $this->params['fromRoot'] = ($extra_url_params[5]);
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
     * @return string Nnom de l'autorització a fer servir
     */
    public function getAuthorizationType() {
        return "_none";
    }
}
