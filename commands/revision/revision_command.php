<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_INC . "lib/plugins/ajaxcommand/");
require_once (DOKU_COMMAND . "defkeys/PageKeys.php");

/**
 * Class revision_command
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
class revision_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[PageKeys::KEY_ID] = self::T_STRING;
        $this->types[PageKeys::KEY_OFFSET] = self::T_INTEGER;
        $this->types['targetId'] = self::T_STRING;
    }

    /**
     * Cancela la edició.
     * @return string[] array associatiu amb la resposta formatada (id, ns, tittle i content)
     */
    protected function process() {
//        $response['revs'] = $this->modelWrapper->getRevisionsList($this->params);
        $action = $this->modelManager->getActionInstance("RevisionsListAction", $this->getModelWrapper()->getPersistenceEngine());
        $response['revs'] = $action->get($this->params);
        $response['revs']['urlBase'] = "lib/exe/ioc_ajax.php?call=diff";
        $response[PageKeys::KEY_ID] = $this->params['targetId'];
        return $response;
    }

    /**
     * Afegeix una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param mixed $response
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {
        $ret->addRevisionsTypeResponse($response[PageKeys::KEY_ID], $response['revs']);
    }
}
