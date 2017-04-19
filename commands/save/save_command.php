<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
//require_once (DOKU_COMMAND.'DokuModelWrapper.php');

/**
 * Class save_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class save_command extends abstract_command_class {

    /**
     * El constructor estableix els tipus per 'id', 'rev', 'range', 'date', 'prefix', 'suffix', 'changecheck', 'target'
     * i 'summary', i el valor per defecte de 'id' a 'index' que s'estableix com a paràmetre.
     */
    public function __construct() {
        parent::__construct();
        $this->types['id']          = abstract_command_class::T_STRING;
        $this->types['rev']         = abstract_command_class::T_STRING;
        $this->types['range']       = abstract_command_class::T_STRING;
        $this->types['date']        = abstract_command_class::T_STRING;
        $this->types['prefix']      = abstract_command_class::T_STRING;
        $this->types['suffix']      = abstract_command_class::T_STRING;
        $this->types['changecheck'] = abstract_command_class::T_STRING;
        $this->types['target']      = abstract_command_class::T_STRING;
        $this->types['summary']     = abstract_command_class::T_STRING;
        $this->types['minor']     = abstract_command_class::T_BOOLEAN;

        $this->types['reload']     = abstract_command_class::T_BOOLEAN;
        $this->types['cancel']     = abstract_command_class::T_BOOLEAN;

        $defaultValues = array('id' => 'index');
        $this->setParameters($defaultValues);
    }

    /**
     * Guarda la edició i retorna la informació de la pàgina
     *
     * @return array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
//        $ret = $this->modelWrapper->saveEdition(
//                                  $this->params['id'], $this->params['rev'],
//                                  $this->params['range'], $this->params['date'],
//                                  $this->params['prefix'], $this->params['wikitext'],
//                                  $this->params['suffix'], $this->params['summary']
//        );
        $ret = $this->modelWrapper->saveEdition($this->params);
        return $ret;
    }

    /**
     * Afegeix el array passat com argument com resposta de tipus DATA_TYPE al generador de respostes.
     *
     * @param array                    $response informació de la pàgina
     * @param AjaxCmdResponseGenerator $ret      objecte on s'afegeix la resposta
     *
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {
        $ret->addInfoDta(" default ");
    }
}
