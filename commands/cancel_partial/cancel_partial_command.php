<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_INC . "lib/plugins/ajaxcommand/");
require_once(DOKU_COMMAND . 'defkeys/PageKeys.php');
/**
 * Class cancel_partial_command
 * @author Josep Cañellas <jcanell4@ioc.cat>, Xavier García <xaviergaro.dev@gmail.com>
 */
class cancel_partial_command extends abstract_command_class {

    /**
     * El constructor estableix els tipus per 'id', 'rev', 'range', 'date', 'prefix', 'suffix', 'changecheck', 'target'
     * i 'summary', i el valor per defecte de 'id' a 'index' que s'estableix com a paràmetre.
     */
    public function __construct() {
        parent::__construct();
        $this->types[PageKeys::KEY_ID] = self::T_STRING;
        $this->types[PageKeys::KEY_REV] = self::T_STRING;
        $this->types[PageKeys::KEY_RANGE] = self::T_STRING;
        $this->types[PageKeys::KEY_DATE] = self::T_STRING;
        $this->types[PageKeys::KEY_PRE] = self::T_STRING;
        $this->types[PageKeys::KEY_SUF] = self::T_STRING;
        $this->types[PageKeys::CHANGE_CHECK] = self::T_STRING;
        $this->types[PageKeys::KEY_TARGET] = self::T_STRING;
        $this->types[PageKeys::KEY_SUM] = self::T_STRING;
        $this->types[PageKeys::KEY_TO_REQUIRE] = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_KEEP_DRAFT] = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_UNLOCK] = self::T_BOOLEAN;
        $this->types[PageKeys::DISCARD_CHANGES] = self::T_BOOLEAN;

        $this->setParameters([PageKeys::KEY_ID => "index"]);
    }

    /**
     * Guarda la edició i retorna la informació de la pàgina
     * @return array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        $editingChunks = explode(',', $this->params[PageKeys::KEY_IN_EDITING_CHUNKS]);
        $key = array_search($this->params[PageKeys::KEY_SECTION_ID], $editingChunks);

        if ($key !== false) {
            unset($editingChunks[$key]);
        }
        $this->params[PageKeys::KEY_EDITING_CHUNKS]=$editingChunks;
        $contentData = $this->modelWrapper->cancelPartialEdition($this->params);

        return $contentData;
    }

    /**
     * Afegeix el array passat com argument com resposta de tipus DATA_TYPE al generador de respostes.
     * @param array $response informació de la pàgina
     * @param AjaxCmdResponseGenerator $ret objecte on s'afegeix la resposta
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {
        $ret->addInfoDta(" default ");
    }

    public function getAuthorizationType() {
        return "cancel";
    }
}
