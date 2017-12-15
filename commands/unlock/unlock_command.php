<?php
if (!defined('DOKU_INC')) die();

/**
 * Class cancel_command
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
class unlock_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[AjaxKeys::KEY_ID] = self::T_STRING;
    }

    /**
     * Cancela la edició.
     * @return string[] array associatiu amb la resposta formatada (id, ns, tittle i content)
     */
    protected function process() {
        return $this->modelAdapter->unlock($this->params['id']);
    }

    /**
     * Afegeix una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param mixed $response
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {
        $ret->addInfoDta($response['info']);
        $ret->addRefreshLock(
                    $response[AjaxKeys::KEY_ID],
                    $response[AjaxKeys::KEY_NS],
                    $response['timeout']
                );
    }
}
