<?php
if(!defined('DOKU_INC')) die();

/**
 * Class new_page_command
 */
class new_page_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types[AjaxKeys::KEY_ID] = self::T_STRING;
        $this->types[ProjectKeys::KEY_TEMPLATE] = self::T_STRING;
    }

    /**
     * Retorna la pàgina corresponent a la 'id' i 'rev'.
     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        PagePermissionManager::updateMyOwnPagePermission($this->authorization->getPermission());
        $pageModel = new DokuPageModel($this->modelAdapter->getPersistenceEngine());

        //sólo se ejecuta si no existe un proyecto en la ruta especificada
        if (!$pageModel->existProject($this->params[AjaxKeys::KEY_ID])) {
            $action = new CreatePageAction($this->modelAdapter->getPersistenceEngine());
            $contentData = $action->get($this->params);
        }
        if ($contentData)
            return $contentData;
        else
            throw new CantCreatePageInProjectException();
    }

    /**
     * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     * @param array                    $contentData array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     * @return void
     */
    protected function getDefaultResponse($contentData, &$responseGenerator) {
        $responseGenerator->addHtmlDoc(
                            $contentData[AjaxKeys::KEY_ID],
                            $contentData[AjaxKeys::KEY_NS],
                            $contentData["title"],
                            $contentData["content"]
                        );
    }
}
