<?php
/**
 * Class get_toc_page_command: Busca la Table Of Contents de una página
 * @author rafael <rclaver@ioc.cat>
 */
if (!defined('DOKU_INC')) die();

class get_toc_page_command extends abstract_command_class {

    public function init($modelManager = NULL) {
        parent::init($modelManager);
        $this->authenticatedUsersOnly = FALSE;
        $this->throwsEventResponse = FALSE;
    }

    /**
     * Busca la Table Of Contents d'una página
     * @return JSON {'htmlTOC': 'elementos del TOC'}
     */
    public function process() {
        $action = $this->getModelManager()->getActionInstance("GetTocAction");
        $toc = $action->get($this->params);
        return $toc;
    }

    function getDefaultResponse($response, &$ret) {
        $ret->setEncodedResponse($response);
    }

    /**
     * @return string Nnom de l'autorització a fer servir
     */
    public function getAuthorizationType() {
        return "_none";
    }
}
