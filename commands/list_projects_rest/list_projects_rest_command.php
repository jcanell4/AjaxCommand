<?php
/**
 * list_projects_rest_command: Lista de tipos de proyecto
 * @culpable Rafael Claver
 */
if(!defined('DOKU_INC')) die();

class list_projects_rest_command extends abstract_rest_command_class {

    public function init( $modelManager = NULL ) {
        parent::init($modelManager);
        $this->authenticatedUsersOnly = FALSE;
    }

    /**
     * Solicita la lista de tipos de proyecto válidos
     * @return json
     */
    public function processGet() {
        $action = $this->getModelManager()->getActionInstance("ListProjectsAction");
        $projectMetaData = $action->get($this->params);
        return $projectMetaData;
    }

    /**
     * Extreu els paràmetres de la url passada com argument i els estableix com a paràmetres de l'objecte.
     * @param string[] $extra_url_params paràmetres per extreure
     */
    public function setParamValuesFromUrl($extra_url_params) {
        if ($extra_url_params && is_array($extra_url_params)) {
            if ($extra_url_params[0])
                $this->params['projectType'] = $extra_url_params[0];
            if ($extra_url_params[1])
                $this->params[ProjectKeys::KEY_ID] = $extra_url_params[1];
            if ($extra_url_params[2])
                $this->params['list_type'] = $extra_url_params[2];
        }
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
