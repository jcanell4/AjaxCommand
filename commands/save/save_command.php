<?php
if(!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_INC."lib/plugins/ajaxcommand/");
require_once (DOKU_COMMAND . "defkeys/PageKeys.php");

/**
 * Class save_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class save_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[PageKeys::KEY_ID]        = self::T_STRING;
        $this->types[PageKeys::KEY_REV]       = self::T_STRING;
        $this->types[PageKeys::KEY_RANGE]     = self::T_STRING;
        $this->types[PageKeys::KEY_DATE]      = self::T_STRING;
        $this->types[PageKeys::KEY_PRE]       = self::T_STRING;
        $this->types[PageKeys::KEY_SUF]       = self::T_STRING;
        $this->types[PageKeys::CHANGE_CHECK]  = self::T_STRING;
        $this->types[PageKeys::KEY_TARGET]    = self::T_STRING;
        $this->types[PageKeys::KEY_SUM]       = self::T_STRING;
        $this->types[PageKeys::KEY_MINOR]     = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_RELOAD]    = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_CANCEL]    = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_KEEP_DRAFT]= self::T_BOOLEAN;

        $this->setParameters([PageKeys::KEY_ID => "index"]);
    }

    /**
     * Guarda la edició i retorna la informació de la pàgina
     * @return array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        $params = array(PageKeys::KEY_ID       => $this->params[PageKeys::KEY_ID],
                        PageKeys::KEY_DATE     => $this->params[PageKeys::KEY_DATE],
                        PageKeys::KEY_PRE      => $this->params[PageKeys::KEY_PRE],
                        PageKeys::KEY_TARGET   => $this->params[PageKeys::KEY_TARGET],
                        PageKeys::KEY_WIKITEXT => $this->params[PageKeys::KEY_WIKITEXT]
                  );
        $action = $this->getModelManager()->getActionInstance("SavePageAction");
        $content = $action->get($params);
        return $content;
    }

    /**
     * Afegeix el array passat com argument com resposta de tipus DATA_TYPE al generador de respostes.
     * @param array                    $response informació de la pàgina
     * @param AjaxCmdResponseGenerator $ret      objecte on s'afegeix la resposta
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {
        $ret->addInfoDta(" default ");
    }
}
