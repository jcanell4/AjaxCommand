<?php
/**
 * Class cancel_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if (!defined('DOKU_INC')) die();

class cancel_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[PageKeys::KEY_ID] = self::T_STRING;
        $this->types[PageKeys::KEY_AUTO] = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_DISCARD_DRAFT] = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_KEEP_DRAFT] = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_NO_RESPONSE] = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_UNLOCK] = self::T_BOOLEAN;

        $this->setParameterDefaultValues(array(PageKeys::KEY_NO_RESPONSE => FALSE));
    }

    /**
     * Cancela la edició.
     * @return string[] array associatiu amb la resposta formatada (id, ns, tittle i content)
     */
    protected function process() {
        $action = $this->getModelManager()->getActionInstance("CancelEditPageAction", ['format' => $this->getFormat()]);
        $content = $action->get($this->params);
        return $content;
    }

    /**
     * Afegeix una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param mixed $contentData // TODO[Xavi] No es fa servir per a res?
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     */
    protected function getDefaultResponse($contentData, &$ret) {
        //TODO[Xavi] $contentData no te cap valor?
        $ret->addHtmlDoc(
                $contentData[PageKeys::KEY_ID],
                $contentData[PageKeys::KEY_NS],
                $contentData[PageKeys::KEY_TITLE],
                $contentData[PageKeys::KEY_CONTENT]
            );
    }
}
