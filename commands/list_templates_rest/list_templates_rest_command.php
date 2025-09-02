<?php
/**
 * Retorna un array en formato JSON que contiene la lista de plantillas de documentos
 * @culpable Rafael Claver
 */
if (!defined('DOKU_INC')) die();

class list_templates_rest_command extends abstract_rest_command_class {

    public function init( $modelManager = NULL ) {
        parent::init($modelManager);
        $this->authenticatedUsersOnly = FALSE;
    }

    /**
     * Obté la llista de plantilles del fitxer de configuració
     * @return string llista en format JSON
     */
    public function processGet() {
        $action = $this->getModelManager()->getActionInstance("ListTemplatesAction");
        $projectMetaData = $action->get($this->params);
        return $projectMetaData;
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
