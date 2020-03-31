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
        if (!$pageModel->haveAnyDirProject($this->params[PageKeys::KEY_OLD_NAME])) {
            $action = $this->getModelManager()->getActionInstance("RenameFolderAction");
            $action->get($this->params);
        }else {
            throw new Exception("No és permés el canvi de nom. La ruta sol·licitada conté directoris de projecte");
        }
        return TRUE;
    }

    /**
     * Afegeix un missatge al generador de respostes.
     * @param array                    $responseData
     * @param AjaxCmdResponseGenerator $responseGenerator
     * @return void
     */
    protected function getDefaultResponse($responseData, &$responseGenerator) {
        $responseGenerator->addInfoDta(AjaxCmdResponseGenerator::generateInfo('info', WikiIocLangManager::getLang("renamed"),NULL,20));
    }
}
