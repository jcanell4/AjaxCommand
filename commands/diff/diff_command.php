<?php
if (!defined('DOKU_INC')) die();
/**
 * Class diff_command
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
class diff_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types['id']  = self::T_STRING;
        $this->types['rev'] = self::T_STRING;
        $this->types['rev2'] = self::T_ARRAY;
    }

    /**
     * Retorna la pàgina corresponent a la 'id' i 'rev'.
     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        // TODO[Xavi] Al getDiffPage s'haura de pasar un array amb 2 valors amb el nom 'rev2' per comparar 2 revisions
        $contentData = $this->modelAdapter->getDiffPage(
                $this->params['id'],
                $this->params['rev'],
                $this->params['rev2']
        );
        return $contentData;
    }

    /**
     * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param array                    $response array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {
        global $lang;
        $ret->addInfoDta("info", $lang['draftdate'].' '.dformat(), $this->types['id']);
    }
}