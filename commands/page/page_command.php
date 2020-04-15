<?php
/**
 * Class page_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if (!defined('DOKU_INC')) die();

class page_command extends abstract_command_class
{

    protected $defaultFormat = 'wiki';

    public function __construct()
    {
        parent::__construct();
        $this->types[PageKeys::KEY_ID] = self::T_STRING;
        $this->types[PageKeys::KEY_REV] = self::T_STRING;
        $this->setParameters([PageKeys::KEY_ID => PageKeys::DW_DEFAULT_PAGE]);
    }

    /**
     * Retorna la pàgina corresponent a la 'id' i 'rev'.
     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
     */
    protected function process()
    {

        $format = $this->getFormat();

        if ($this->params[PageKeys::KEY_REV]) {
            $action = $this->getModelManager()->getActionInstance("HtmlRevisionPageAction", ['format' => $format]);
        } else {
            $action = $this->getModelManager()->getActionInstance("HtmlPageAction", ['format' => $format]);
        }
        $response = $action->get($this->params);
        return $response;
    }

    /**
     * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param array                    $response array amb informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     * @return void
     */
    protected function getDefaultResponse($response, &$responseGenerator) {
    	$responseGenerator->addHtmlDoc(
		$response[PageKeys::KEY_ID],
                $response[PageKeys::KEY_NS],
		$response[PageKeys::KEY_TITLE],
                $response[PageKeys::KEY_CONTENT]
	);
    }

}
