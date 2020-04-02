<?php
/**
 * rename_folder_command: Renombra un directorio y modifica los archivos cuyo nombre haya sido constuido
 *                        a partir de la wiki ruta y modifica las referencias a esa ruta
 *                        dentro del contenido de los archivos existentes a partir de la ruta cambiada.
 */
if(!defined('DOKU_INC')) die();

class rename_folder_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
    }

    /**
     * @return array
     */
    protected function process() {
        PagePermissionManager::updateMyOwnPagePermission($this->authorization->getPermission());
        $action = $this->getModelManager()->getActionInstance("RenameFolderAction");
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
        $responseGenerator->addInfoDta(AjaxCmdResponseGenerator::generateInfo('info', WikiIocLangManager::getLang("renamed"),NULL,20));
    }
}
