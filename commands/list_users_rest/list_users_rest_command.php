<?php
/**
 * list_users_rest_command: Lista de usuarios
 * @culpable Rafael Claver
 */
if(!defined('DOKU_INC')) die();

class list_users_rest_command extends abstract_rest_command_class {

    public function init( $modelManager = NULL ) {
        parent::init($modelManager);
        $this->authenticatedUsersOnly = FALSE;
    }

    /**
     * Solicita la lista de usuarios
     * @return json
     */
    public function processGet() {
        $action = $this->getModelManager()->getActionInstance("ListUsersAction");
        $data = $action->get($this->params);
        return json_encode($data);
    }

    function getDefaultResponse($response, &$ret) {
        if ($response['meta']) {
            $ret->addMetadata($response['id'], $response['meta']);
        }
        $ret->setEncodedResponse($response);
    }

    /** @return string nom del 'command' corresponent a l'autorització què es vol fer servir */
    public function getAuthorizationType() {
        return "_none";
    }
}
