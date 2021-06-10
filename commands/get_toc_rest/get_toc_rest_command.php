<?php
/**
 * Class get_toc_rest_command: Busca la Table Of Contents de una página
 * @author rafael <rclaver@ioc.cat>
 */
if (!defined('DOKU_INC')) die();

class get_toc_rest_command extends abstract_rest_command_class {

    public function init($modelManager = NULL) {
        parent::init($modelManager);
        $this->authenticatedUsersOnly = FALSE;
    }

    /**
     * Busca la Table Of Contents d'una página
     * @return JSON {'html': 'elementos del TOC'}
     */
    public function processGet() {
        $action = $this->getModelManager()->getActionInstance("GetTocAction");
        $toc = $action->get($this->params);
        return $toc;
    }

    function getDefaultResponse($response, &$ret) {
	$ret->setEncodedResponse($response);
    }

    /**
     * @return string Nnom de l'autorització a fer servir
     */
    public function getAuthorizationType() {
        return "_none";
    }
}
