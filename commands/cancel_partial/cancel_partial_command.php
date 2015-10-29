<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
//require_once (DOKU_COMMAND.'DokuModelWrapper.php');

/**
 * Class save_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>, Xavier García <xaviergaro.dev@gmail.com>
 */
class cancel_partial_command extends abstract_command_class
{

    /**
     * El constructor estableix els tipus per 'id', 'rev', 'range', 'date', 'prefix', 'suffix', 'changecheck', 'target'
     * i 'summary', i el valor per defecte de 'id' a 'index' que s'estableix com a paràmetre.
     */
    public function __construct()
    {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
        $this->types['rev'] = abstract_command_class::T_STRING;
        $this->types['range'] = abstract_command_class::T_STRING;
        $this->types['date'] = abstract_command_class::T_STRING;
        $this->types['prefix'] = abstract_command_class::T_STRING;
        $this->types['suffix'] = abstract_command_class::T_STRING;
        $this->types['changecheck'] = abstract_command_class::T_STRING;
        $this->types['target'] = abstract_command_class::T_STRING;
        $this->types['summary'] = abstract_command_class::T_STRING;

        $defaultValues = array(
            'id' => 'index'
        );

        $this->setParameters($defaultValues);
    }

    /**
     * Guarda la edició i retorna la informació de la pàgina
     *
     * @return array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     */
    protected function process()
    {
        $ret = null;
        $editingChunks = explode(',', $this->params['editing_chunks']);

        $key = array_search($this->params['section_id'], $editingChunks);

        if ($key !== false) {
            unset($editingChunks[$key]);
        }


        if (count($editingChunks) ===0) {
//            $this->modelWrapper->cancelEdition(
//                $this->params['id'],
//                $this->params['rev'],
//                $this->params['keep_draft'] //TODO[Xavi] això no es fa servir, ho deixem per quan implementem els esborranys
//            );


        }

        $ret = $this->modelWrapper->cancelPartialEdition( // No fa falta actualitzar la data ni els rangs, només el html, així que no cal cridar al SetFormInputValueForPartials
            $this->params['id'], $this->params['summary'],
            $this->params['date'],
//                $this->params['id'], $this->params['rev'],
//                $this->params['range'], $this->params['date'],
//                $this->params['prefix'], $this->params['wikitext'],
//                $this->params['suffix'], $this->params['summary'],
            $this->params['section_id'], $editingChunks
        );


        return $ret;
    }

    /**
     * Afegeix el array passat com argument com resposta de tipus DATA_TYPE al generador de respostes.
     *
     * @param array $response informació de la pàgina
     * @param AjaxCmdResponseGenerator $ret objecte on s'afegeix la resposta
     *
     * @return void
     */
    protected function getDefaultResponse($response, &$ret)
    {
        $ret->addInfoDta(" default ");
    }
}
