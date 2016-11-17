<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_PLUGIN . 'wikiiocmodel/projects/defaultProject/PermissionPageForUserManager.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_COMMAND . 'commands/new_page/new_page_command.php');
require_once(DOKU_COMMAND . 'requestparams/ResponseParameterKeys.php');

/**
 * Class page_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class new_shortcuts_page_command extends new_page_command
{

    protected function process()
    {
        PermissionPageForUserManager::updatePermission($this->authorization->getPermission());
        $contentData = $this->modelWrapper->createPage($this->params);

        $user_id = WikiIocInfoManager::getInfo('client');
        $contentData['shortcuts'] = $this->getModelWrapper()->getShortcutsTaskList($user_id);
        $contentData['shortcuts']['url_base'] = "lib/plugins/ajaxcommand/ajax.php?call=page";

        return $contentData;
    }

    /**
     * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     *
     * @param array $contentData array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     *
     * @return void
     */
    protected function getDefaultResponse($contentData, &$responseGenerator)
    {

        // ALERTA[Xavi] No es pot posar aquí el codi per que cal cridar al PageResponseHandler (parent del responsehandler) perquè carregui la pàgina creada

    }

    public function getAuthorizationType()
    {
        return "new_page";
    }
}
