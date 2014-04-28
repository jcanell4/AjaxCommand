<?php
/**
 * @author     Josep Cañellas <jcanell4@ioc.cat>
 */

// must be run within Dokuwiki
if (!defined('DOKU_INC')) die();

if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'action.php');

class action_plugin_ajaxcommand extends DokuWiki_Action_Plugin {
    function register(&$controller) {
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this,
                                   'runAjaxCall');
        $controller->register_hook('WIOC_PROCESS_RESPONSE_edit', 'AFTER', $this,
                                   'processCmd');
    }

    function processCmd(&$event, $param) {
        if($event->data!=null){
            $event->data["ajaxCmdesponseGenerator"]->addProcessFunction(true, 
                            "ioc/dokuwiki/processAceEditor",
                            array(
                                "key"=>"edit_ace",
                                "buttonId" => WikiIocCfg::Instance()->getConfig("saveButton"),
                                "textAreaId" => 'wiki__text',
                            ));
        }
    }
    
    function runAjaxCall(&$event, $param) {
        global $INFO;
        
        $call = $event->data['command'];
        $event->preventDefault();
        if(!auth_isadmin()){
            print ('fobiben! for admins only  ');
        }else{
            print 'Ok! You are an admin ';
        }
        if(!checkSecurityToken()){
            print ('CRSF Attack' . 'fora: ' . $_SERVER['REMOTE_USER']);
        }else{
        
            print "Hola usuari: " .  $_SERVER['REMOTE_USER'] .". Vols executar: ". $call;        
        }
    }
}