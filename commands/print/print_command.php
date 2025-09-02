<?php
/**
 * Class print_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if (!defined('DOKU_INC')) die();

class print_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
	$this->types[PageKeys::KEY_ID]  = self::T_STRING;
	$this->types[PageKeys::KEY_REV] = self::T_STRING;

	$this->setParameters( [PageKeys::KEY_ID => PageKeys::DW_DEFAULT_PAGE] );
    }

    /**
     * Retorna la pàgina corresponent a la 'id' i 'rev'.
     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        $action = $this->getModelManager()->getActionInstance("PrintPageAction");
        $ret = $action->get($this->params);
        return $ret;
    }

    /**
     * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param array                    $contentData array amb informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     * @return void
     */
    protected function getDefaultResponse( $contentData, &$responseGenerator ) {
    	$responseGenerator->addHtmlDoc(
		$contentData[PageKeys::KEY_ID],
                $contentData[PageKeys::KEY_NS],
		$contentData[PageKeys::KEY_TITLE],
                $contentData[PageKeys::KEY_CONTENT]
	);
    }

    public function getAuthorizationType() {
        return "page";
    }

}
