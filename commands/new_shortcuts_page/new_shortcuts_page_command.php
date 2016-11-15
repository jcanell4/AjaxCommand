<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_PLUGIN . 'wikiiocmodel/projects/defaultProject/PermissionPageForUserManager.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_COMMAND . 'commands/new_page/new_page_command.php');

/**
 * Class page_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class new_shortcuts_page_command extends new_page_command {

//    /**
//     * El constructor estableix els tipus de 'id' i 'rev' i el valor per defecte de 'id' com a 'start'. i l'estableix
//     * com a paràmetre.
//     */
//    public function __construct() {
//        parent::__construct();
//        $this->types['id'] = abstract_command_class::T_STRING;
//        $this->types['template'] = abstract_command_class::T_STRING;
//        //$this->permissionFor =  DokuModelAdapter::ADMIN_PERMISSION;
//    }
//
//    /**
//     * Retorna la pàgina corresponent a la 'id' i 'rev'.
//     *
//     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
//     */
    protected function process() {
        PermissionPageForUserManager::updatePermission($this->authorization->getPermission());
        $contentData = $this->modelWrapper->createPage($this->params);
        return $contentData;
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

//        parent::response($requestParams, $responseData, $ajaxCmdResponseGenerator);
        $responseGenerator->addAddItemTree(cfgIdConstants::TB_INDEX, $this->params[PageKeys::KEY_ID]);

        $user_id = WikiIocInfoManager::getInfo("userinfo");

        $dades = $this->getModelWrapper()->getShortcutsTaskList($user_id);
//            $dades = $this->getModelWrapper()->getShortcutsTaskList();
        $urlBase = "lib/plugins/ajaxcommand/ajax.php?call=page";

        $responseGenerator->addShortcutsTab(cfgIdConstants::ZONA_NAVEGACIO,
            cfgIdConstants::TB_SHORTCUTS,
            $dades['title'],
            $dades['content'],
            $urlBase);

    }
}
