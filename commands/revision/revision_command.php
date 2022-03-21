<?php
/**
 * Class revision_command
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
if (!defined('DOKU_INC')) die();

class revision_command extends abstract_writer_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[PageKeys::KEY_ID] = self::T_STRING;
        $this->types[PageKeys::KEY_OFFSET] = self::T_INTEGER;
        $this->types['targetId'] = self::T_STRING;
    }

    /**
     * Genera la llista de revisions per a les metadades
     * @return string[] array associatiu amb la resposta formatada (id, ns, tittle i content)
     */
    protected function process() {
        $this->params[PageKeys::PROJECT_TYPE] = $this->getModelManager()->getProjectType();
        if ($this->params[PageKeys::PROJECT_TYPE] == "defaultProject") {
            $action = $this->getModelManager()->getActionInstance("RevisionsListAction");
            $response[PageKeys::KEY_REVISIONS] = $action->get($this->params);
            $response[PageKeys::KEY_REVISIONS]['urlBase'] = "lib/exe/ioc_ajax.php?call=diff";
            $response[PageKeys::KEY_ID] = $this->params['targetId'];
        }else {
            $action = $this->getModelManager()->getActionInstance("RevisionsProjectListAction");
            $response = $action->get($this->params);
            $response[ProjectKeys::KEY_REV]['urlBase'] = "lib/exe/ioc_ajax.php?call=diff";
        }
        return $response;
    }

    /**
     * Afegeix una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param mixed $response
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {
        $revs = ($this->params[PageKeys::PROJECT_TYPE] == "defaultProject") ? $response[AjaxKeys::KEY_REVISIONS] : $response[ProjectKeys::KEY_REV];
        $ret->addRevisionsTypeResponse($response[PageKeys::KEY_ID], $revs);
    }
}
