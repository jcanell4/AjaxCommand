<?php
if(!defined('DOKU_INC')) die();

/**
 * Class draft_command
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
class draft_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types['id'] = self::T_STRING;
    }

    /**
     * Cancela la edició.
     * @return string[] array associatiu amb la resposta formatada (id, ns, tittle i content)
     */
    protected function process() {
        return $this->modelWrapper->draft($this->params);
    }

    /**
     * Afegeix una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param mixed                    $response
     * @param AjaxCmdResponseGenerator $ret      objecte al que s'afegirà la resposta
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {
        if ($response['lockInfo']){
            $timeout =  ($response["lockInfo"]["locker"]["time"] + WikiGlobalConfig::getConf("locktime") - 60 - time()) * 1000;
            $ret->addRefreshLock($response["id"], $this->params["id"], $timeout);
        }
        if (isset($response['info'])){
            $ret->addInfoDta($response['info']);
        }else{
            $ret->addCodeTypeResponse(0);
        }
    }
}
