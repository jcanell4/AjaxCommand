<?php
/**
 * Class
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . "defkeys/ProjectKeys.php");
require_once(DOKU_COMMAND . 'abstract_command_class.php');

class new_page_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
        $this->types['template'] = abstract_command_class::T_STRING;
        //$this->permissionFor =  DokuModelAdapter::ADMIN_PERMISSION;
    }

    /**
     * Retorna la pàgina corresponent a la 'id' i 'rev'.
     *
     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        PermissionPageForUserManager::updatePermission($this->authorization->getPermission());
        $pageModel = new DokuPageModel($this->modelWrapper->getPersistenceEngine());
        //sólo se ejecuta si no existe un proyecto en la ruta especificada
        if (!$pageModel->existProject($this->params[ProjectKeys::KEY_ID])) {
            $action = new CreatePageAction($this->modelWrapper->getPersistenceEngine());
            $contentData = $action->get($this->params);
        }
        if ($contentData)
            return $contentData;
        else
            throw new CantCreatePageInProjectException();
    }

    /**
     * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     *
     * @param array                    $contentData array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     *
     * @return void
     */
    protected function getDefaultResponse($contentData, &$responseGenerator) {
        $responseGenerator->addHtmlDoc(
                          $contentData["id"], $contentData["ns"],
                          $contentData["title"], $contentData["content"]
        );
    }
}
