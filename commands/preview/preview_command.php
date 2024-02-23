<?php
/**
 * Class preview_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if (!defined('DOKU_INC')) die();

class preview_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
	$this->types[PageKeys::KEY_ID]  = self::T_STRING;
	$this->types[PageKeys::KEY_TEXT] = self::T_STRING;
    }

    /**
     * Retorna la pàgina corresponent a la 'id' i 'rev'.
     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        $action = $this->getModelManager()->getActionInstance("PreviewAction");
        $ret = $action->get($this->params);
        return $ret;
    }

    /**
     * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param array                    $contentData array amb informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     * @return void
     */
    protected function getDefaultResponse( $contentData, &$responseGenerator ) {}

    public function getAuthorizationType() {
        return "page";
    }

}
