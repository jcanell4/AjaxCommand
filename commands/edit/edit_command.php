<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_INC . "lib/plugins/ajaxcommand/");
require_once(DOKU_COMMAND . "defkeys/PageKeys.php");

/**
 * Class edit_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class edit_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[PageKeys::KEY_ID] = self::T_STRING;
        $this->types[PageKeys::KEY_DO] = self::T_STRING;
        $this->types[PageKeys::KEY_REV] = self::T_STRING;
        $this->types[PageKeys::KEY_RANGE] = self::T_STRING;
        $this->types[PageKeys::KEY_SUM] = self::T_STRING;
        $this->types[PageKeys::KEY_RECOVER_LOCAL_DRAFT] = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_RECOVER_DRAFT] = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_TO_REQUIRE] = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_REFRESH] = self::T_BOOLEAN;

        $this->setParameters([PageKeys::KEY_DO => 'edit']);
    }

    /**
     * Retorna el contingut de la página segons els paràmetres emmagatzemats en aquest command.
     * @return array amb el contingut de la pàgina (id, ns, tittle i content)
     */
    protected function process() {

        if ($this->params["refresh"]){
            $contentData = $this->refreshEdition();
        }else{
            $contentData = $this->getEditionPage();
        }
        return $contentData;
    }

    private function refreshEdition() {
        $action = $this->getModelManager()->getActionInstance("RefreshEditionAction", ['format' => $this->getFormat()]);
        $contentData = $action->get($this->params);
        return $contentData;
    }

    private function getEditionPage() {
        $action = $this->getModelManager()->getActionInstance("RawPageAction", ['format' => $this->getFormat()]);
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
                $response["id"],
                $response["ns"],
                $response["title"],
                $response["content"]
            );
    }
}
