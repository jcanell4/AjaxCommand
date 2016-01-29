<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

/**
 * Class cancel_command
 *
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
class unlock_command extends abstract_command_class {

    /**
     * Constructor per defecte que estableix el tipus id.
     */
    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
    }

    /**
     * Cancela la edició.
     *
     * @return string[] array associatiu amb la resposta formatada (id, ns, tittle i content)
     */
    protected function process() {
        return $this->modelWrapper->unlock($this->params['id']);
    }

    /**
     * Afegeix una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     *
     * @param mixed                    $response
     * @param AjaxCmdResponseGenerator $ret      objecte al que s'afegirà la resposta
     *
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {

        $ret->addInfoDta($response['info']);

    }
}
