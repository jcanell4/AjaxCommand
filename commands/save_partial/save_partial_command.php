<?php
/**
 * Class save_partial_command
 * @author Josep Cañellas <jcanell4@ioc.cat>, Xavier García <xaviergaro.dev@gmail.com>
 */
if (!defined('DOKU_INC')) die();

class save_partial_command extends abstract_writer_command_class {
    /**
     * El constructor estableix els tipus per 'id', 'rev', 'range', 'date', 'prefix', 'suffix', 'changecheck', 'target'
     * i 'summary', i el valor per defecte de 'id' a 'index' que s'estableix com a paràmetre.
     */
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
        $this->types[PageKeys::KEY_CANCEL]    = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_CLOSE]     = self::T_BOOLEAN;
        $this->types[PageKeys::KEY_KEEP_DRAFT]= self::T_BOOLEAN;

        $this->setParameters([PageKeys::KEY_ID => "index"]);
    }

    /**
     * Guarda la edició i retorna la informació de la pàgina
     * @return array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        if (isset($this->params[PageKeys::KEY_IN_EDITING_CHUNKS]) && !is_array($this->params[PageKeys::KEY_IN_EDITING_CHUNKS])) {
            $this->params[PageKeys::KEY_IN_EDITING_CHUNKS] = explode(',', $this->params[PageKeys::KEY_IN_EDITING_CHUNKS]);
        }

        $this->params[PageKeys::KEY_EDITING_CHUNKS] = $this->params[PageKeys::KEY_IN_EDITING_CHUNKS];

        if ($this->params[PageKeys::KEY_DO] === 'save_all') {
            $toSaveChunks = json_decode($this->params['chunk_params'], true);

            for ($i = 0; $i < count($toSaveChunks); $i++) {

                $toSaveChunks[$i][PageKeys::KEY_CANCEL_ALL] = $this->params[PageKeys::KEY_CANCEL];
                if ($this->params[PageKeys::KEY_CLOSE]) {
                    $toSaveChunks[$i][PageKeys::KEY_CLOSE] =$this->params[PageKeys::KEY_CLOSE];
                }
                $action = $this->getModelManager()->getActionInstance("SavePartialPageAction");
                $contentData = $action->get($toSaveChunks[$i]);

                // Actualitzem el changecheck pel següent chunk
                if ($i < count($toSaveChunks) - 1) {
                    $toSaveChunks[$i + 1]['date'] = $contentData['structure']['date']+ $i + 2; // Afegim 1ms de diferencia entre cadascun per evitar els conflictes

                }else {
                    $contentData['inputs']['date']+= $i + 1;
                }

            }
        }else {
            $action = $this->getModelManager()->getActionInstance("SavePartialPageAction");
            $contentData = $action->get($this->params);
        }
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
}
