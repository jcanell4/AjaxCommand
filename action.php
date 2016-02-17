<?php
// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_TPL_INCDIR')) define('DOKU_TPL_INCDIR', action_plugin_ajaxcommand_tplIncDir());
require_once(DOKU_PLUGIN . 'action.php');
require_once(DOKU_INC . 'inc/template.php');
if (file_exists(DOKU_TPL_INCDIR."conf/cfgIdConstants.php")){
    require_once(DOKU_TPL_INCDIR."conf/cfgIdConstants.php");
}

function action_plugin_ajaxcommand_tplIncDir() {
    global $conf;
    if (is_callable('tpl_incdir')) {
        $ret = tpl_incdir();
    } else {
        $ret = DOKU_INC . 'lib/tpl/' . $conf['template'] . '/';
    }
    return $ret;
}

/**
 * Class action_plugin_ajaxcommand
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class action_plugin_ajaxcommand extends DokuWiki_Action_Plugin {

    //TODO[Xavi] Pendent d'eliminar o adaptar per una altre tasca, ja no existeix el processAceEditor
    /**
     * Aquest mètode registra els handlers del plugin als events de la DokuWiki.
     *
     * @param Doku_Event_Handler $controller controlador d'events de la DokuWiki
     */
    function register(&$controller) {
//        $controller->register_hook(
//                   'WIOC_PROCESS_RESPONSE_edit', 'AFTER', $this,
//                   'processCmd'
//        );
        //[END TODO]
    }
//
//    /**
//     * @param Doku_Event $event
//     * @param mixed      $param
//     * [TODO Josep] This method should be located in the plugin aceeditor,
//     * but for now, we keep the method here, to  don't modify the plugin aceeditor.
//     */
//    function processCmd(&$event, $param) {
//        if($event->data != NULL && defined("cfgIdConstants::SAVE_BUTTON")) {
//            $event->data["ajaxCmdResponseGenerator"]->addProcessFunction(
//                                                   TRUE,
//                                                   "ioc/dokuwiki/processAceEditor",
//                                                   array(
//                                                       "id"         => $event->data["responseData"]["id"],
//                                                       "key"        => "edit_ace",
//                                                       "buttonId"   => cfgIdConstants::SAVE_BUTTON,
//                                                       "textAreaId" => 'wiki__text',
//                                                   )
//            );
//        }
//    }
}
