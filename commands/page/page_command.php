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
	$this->setParameters( [PageKeys::KEY_ID => 'start'] );
    }

    /**
     * Retorna la pàgina corresponent a la 'id' i 'rev'.
     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        $persistenceEngine = $this->getModelWrapper()->getPersistenceEngine();
        if ($this->params[PageKeys::KEY_REV]) {
            $action = new HtmlRevisionPageAction($persistenceEngine);
        }else{
            $action = new HtmlPageAction($persistenceEngine);
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
		$contentData["title"],
                $contentData["content"]
	);
    }
}
