<?php
/**
 * Class
 * @author Josep Cañellas <jcanell4@ioc.cat>, Xavier García <xaviergaro.dev@gmail.com>
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");

require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_COMMAND . 'defkeys/PageKeys.php');

class save_partial_command extends abstract_command_class {
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
        if (isset($this->params[PageKeys::KEY_IN_EDITING_CHUNKS]) && !is_array($this->params[PageKeys::KEY_IN_EDITING_CHUNKS])) {
            $this->params[PageKeys::KEY_IN_EDITING_CHUNKS] = explode(',', $this->params[PageKeys::KEY_IN_EDITING_CHUNKS]);
        }

        $this->params[PageKeys::KEY_EDITING_CHUNKS] = $this->params[PageKeys::KEY_IN_EDITING_CHUNKS];

        if ($this->params[PageKeys::KEY_DO] === 'save_all') {
            $toSaveChunks = json_decode($this->params['chunk_params'], true);

            for ($i = 0; $i < count($toSaveChunks); $i++) {

                $contentData = $this->modelWrapper->savePartialEdition($toSaveChunks[$i]); // ALERTA[Xavi] Només cal retornar la resposta de l'ultim, la resta de respostes es descarten

                // Actualitzem el changecheck pel següent chunk
                if ($i < count($toSaveChunks) - 1) {
                    $toSaveChunks[$i + 1]['date'] = $contentData['inputs']['date']+ $i + 1; // Afegim 1ms de diferencia entre cadascun per evitar els conflictes

                } else {
                    $contentData['inputs']['date']+= $i + 1;
                }

            }


        } else {
            $contentData = $this->modelWrapper->savePartialEdition($this->params);
        }


//        $contentData = $this->modelWrapper->savePartialEdition(
//            $this->params['id'], $this->params['rev'],
//            $this->params['range'], $this->params['date'],
//            $this->params['prefix'], $this->params['wikitext'],
//            $this->params['suffix'], $this->params['summary'],
//            $this->params['section_id'], explode(',', $this->params['editing_chunks'])
//        );
        return $contentData;
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
