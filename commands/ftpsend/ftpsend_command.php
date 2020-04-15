<?php
/**
 * Class ftpsend_command
 * @culpable Rafael
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_LIB_IOC')) define('DOKU_LIB_IOC', DOKU_INC . "lib/lib_ioc/");
require_once DOKU_LIB_IOC . "ajaxcommand/abstract_command_class.php";

class ftpsend_command extends abstract_command_class {

    public function __construct(){
        parent::__construct();
        $this->types[ProjectKeys::KEY_ID] = self::T_STRING;
        $this->types[ProjectKeys::PROJECT_TYPE] = self::T_STRING;
    }

    protected function process() {
        if ($this->params[ProjectKeys::PROJECT_TYPE]) {
            $action = $this->getModelManager()->getActionInstance("FtpProjectSendAction");
        }else{
            $action = $this->getModelManager()->getActionInstance("FtpSendAction");
        }
        $response = $action->get($this->params);
        return $response;
    }

    protected function getDefaultResponse($responseData, &$ajaxCmdResponseGenerator) {
        if ($responseData[ProjectKeys::KEY_INFO]) {
            $ajaxCmdResponseGenerator->addInfoDta($responseData[ProjectKeys::KEY_INFO]);
        }
        if ($responseData[ResponseHandlerKeys::ALERT]) {
            $ajaxCmdResponseGenerator->addAlert($responseData[ResponseHandlerKeys::ALERT]);
        }
    }

    /**
     * @return string (nom del command, a partir del nom de la clase,
     *                 modificat pels valors de $params per definir subclasses específiques
     *                 amb autoritzacions específiques)
     */
    public function getAuthorizationType() {
        return "ftp";
    }

}
