<?php
/**
 * Class revision_command
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
if (!defined('DOKU_INC')) die();

class revision_command extends abstract_writer_command_class {

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
        $action = $this->getModelManager()->getActionInstance("RevisionsListAction");
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
