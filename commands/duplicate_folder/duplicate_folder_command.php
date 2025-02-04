<?php
/**
 * duplicate_folder_command: Fa un duplicat un directori de materials en un altre carpeta
 */
if(!defined('DOKU_INC')) die();

class duplicate_folder_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
    }

    protected function process() {
        PagePermissionManager::updateMyOwnPagePermission($this->authorization->getPermission());
        $action = $this->getModelManager()->getActionInstance("DuplicateFolderAction");
        $action->get($this->params);
        return TRUE;
    }

    /**
     * Afegeix un missatge al generador de respostes.
     * @param array                    $responseData
     * @param AjaxCmdResponseGenerator $responseGenerator
     * @return void
     */
    protected function getDefaultResponse($responseData, &$responseGenerator) {
        $responseGenerator->addInfoDta(AjaxCmdResponseGenerator::generateInfo('info', WikiIocLangManager::getLang("copied"),NULL,20));
    }
}
