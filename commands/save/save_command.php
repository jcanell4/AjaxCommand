<?php
/**
 * Class save_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if(!defined('DOKU_INC')) die();

class save_command extends abstract_writer_command_class {

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
        $this->types[PageKeys::KEY_WIOCCL_STRUCTURE] = self::T_JSON;

        $params = [PageKeys::KEY_ID => "index"];
        $this->setParameters($params);
    }

    /**
     * Guarda la edició i retorna la informació de la pàgina
     * @return array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        $action = $this->getModelManager()->getActionInstance("SavePageAction", ['format' => $this->getFormat()]);
        $content = $action->get($this->params);
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
