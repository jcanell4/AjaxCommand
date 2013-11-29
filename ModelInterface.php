<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ModelInterface
 *
 * @author professor
 */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once(DOKU_COMMAND.'JsonGenerator.php');

class ModelInterface {
    //put your code here
    function getContentPageResponse($pid, $pdo, $prev){
        global $conf;
        $pageToSend = ModelInterface::getFormatedPage($pid, $pdo, $prev);
        $pageTitle = tpl_pagetitle($pid, true);
        $contentData = array('id' => \str_replace(":", "_",$pageTitle),
                                'title' => $pageTitle,
                                'content' => $pageToSend);
        return $contentData;        
    }
    
    function getLoginPageResponse(){
        return ModelInterface::getContentPageResponse("start", "show", null);                
    }
    
    function getLogoutPageResponse(){
        return array('id' => "logout_info",
        'title' => "desconectat",
        'content' => "Accés restringit. Per accedir cal que us identifiqueu");                
    }
    
    function getFormatedPage($pid, $pdo, $prev){
        global $ID;
        global $ACT;
        global $REV;
        
        $old_id = $ID;
        $old_do = $ACT;
        $old_rev = $REV;
        
        $ID  =  $pid;
        $ACT =  $pdo;
        $REV =  $rpev;
        ob_start();
        trigger_event('TPL_ACT_RENDER', $ACT, "tpl_content_core");
        $html_output = ob_get_clean()."\n";
        $ID  =  $old_id;
        $ACT =  $old_do;
        $REV =  $old_rev;
        return $html_output;
        
    }
}

?>
