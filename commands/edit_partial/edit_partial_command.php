<?php
/**
 * Class edit_partial_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if (!defined('DOKU_INC')) die();

class edit_partial_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[PageKeys::KEY_ID] = self::T_STRING;
        $this->types[PageKeys::KEY_REV] = self::T_STRING;
        $this->types[PageKeys::KEY_RANGE] = self::T_STRING;
        $this->types[PageKeys::KEY_SUM] = self::T_STRING;
        $this->types[PageKeys::KEY_RECOVER_LOCAL_DRAFT] = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_RECOVER_DRAFT] = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_TO_REQUIRE] = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_COPY_REMOTE_DRAFT_TO_LOCAL] = self::T_BOOLEAN;
    }

    /**
     * Retorna el contingut de la página segons els paràmetres emmagatzemats en aquest command.
     * @return array amb el contingut de la pàgina (id, ns, tittle i content)
     */
    protected function process() {

        if (strlen($this->params[PageKeys::KEY_IN_EDITING_CHUNKS]) == 0) {
            $this->params[PageKeys::KEY_EDITING_CHUNKS] = [];
        } else {
            $this->params[PageKeys::KEY_EDITING_CHUNKS] = explode(',', $this->params[PageKeys::KEY_IN_EDITING_CHUNKS]);
            $this->params[PageKeys::KEY_EDITING_CHUNKS][] = $this->params[PageKeys::KEY_SECTION_ID];
        }
        $action = $this->getModelManager()->getActionInstance("RawPartialPageAction");
        $contentData = $action->get($this->params);
        return $contentData;
    }

    /**
     * Afegeix la pàgina passada com argument com una resposta de tipus DATA_TYPE al generador de respostes.
     * @param array $response amb el contingut de la pàgina
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     * @return mixed|void
     */
    protected function getDefaultResponse($response, &$ret) {
        $ret->addWikiCodeDoc(
                $response[AjaxKeys::KEY_ID],
                $response[AjaxKeys::KEY_NS],
                $response[PageKeys::KEY_TITLE],
                $response[PageKeys::KEY_CONTENT]
            );
    }

    public function getAuthorizationType() {
        return "edit";
    }

}