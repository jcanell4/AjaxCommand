<?php
if(!defined('DOKU_INC')) die();

/**
 * Class rename_folder_command
 */
class rename_folder_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return array
     */
    protected function process() {
        PagePermissionManager::updateMyOwnPagePermission($this->authorization->getPermission());
        $pageModel = new DokuPageModel($this->getPersistenceEngine());

        //sólo se ejecuta si no existe un proyecto en la ruta especificada
        if (!$pageModel->haveADirProject($this->params[AjaxKeys::KEY_ID])) {
            $action = $this->getModelManager()->getActionInstance("RenameFolderAction");
            $action->get($this->params);
        }
        return TRUE;
    }

    /**
     * Afegeix un missatge al generador de respostes.
     * @param array                    $contentData
     * @param AjaxCmdResponseGenerator $responseGenerator
     * @return void
     */
    protected function getDefaultResponse($contentData, &$responseGenerator) {
        $responseGenerator->addInfoDta(" default ");
    }
}
