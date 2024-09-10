<?php
/**
 * list_rols_rest_command: Lista de roles
 * @culpable Rafael Claver
 */
if(!defined('DOKU_INC')) die();

class list_rols_rest_command extends abstract_rest_command_class {

    public function init( $modelManager = NULL ) {
        parent::init($modelManager);
        $this->authenticatedUsersOnly = FALSE;
    }

    /**
     * Solicita la lista de roles
     * @return json
     */
    public function processGet() {
        $action = $this->getModelManager()->getActionInstance("ListRolsAction");
        $data = $action->get($this->params);
        return $data;
    }

    function getDefaultResponse( $response, &$ret ) {
        $ret->setEncodedResponse($response);
    }

    /** @return string nom del 'command' corresponent a l'autorització què es vol fer servir */
    public function getAuthorizationType() {
        return "_none";
    }
}
