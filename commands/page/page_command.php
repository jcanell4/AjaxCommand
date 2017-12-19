<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_INC."lib/plugins/ajaxcommand/");
require_once (DOKU_COMMAND . "defkeys/PageKeys.php");

/**
 * Class page_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class page_command extends abstract_command_class {

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
        if ($this->params[PageKeys::KEY_REV]) {
            $action = $this->getModelManager()->getActionInstance("HtmlRevisionPageAction");
        }else{
            $action = $this->getModelManager()->getActionInstance("HtmlPageAction");
        }
        $response = $action->get($this->params);
	return $response;
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
}
