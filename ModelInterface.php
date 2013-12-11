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
//require common


if(!defined('DW_ACT_SHOW')) define('DW_ACT_SHOW',"show/");
if(!defined('DW_ACT_EDIT')) define('DW_ACT_EDIT',"edit/");
//    const DW_ACT_PREVIEW="preview";
//    const DW_ACT_SAVE="save";
//    const DW_ACT_BACKLINK="backlink";
//    const DW_ACT_REVISIONS="revisions";    
//    const DW_ACT_DIFF="diff";
//    const DW_ACT_SUBSCRIBE="subscribe";
//    const DW_ACT_UNSUBSCRIBE="unsubscribe";
//    const DW_ACT_SUBSCRIBENS="subscribens";
//    const DW_ACT_UNSUBSCRIBENS="unsubscribens";
//    const DW_ACT_INDEX="index";
//    const DW_ACT_RECENT="recent";
//    const DW_ACT_SEARCH="search";
//    const DW_ACT_EXPORT_RAW="export_raw";
//    const DW_ACT_EXPORT_XHTML="export_xhtml";
//    const DW_ACT_EXPORT_XHTMLBODY="export_xhtmlbody";
//    const DW_ACT_CHECK="check";
//    const DW_ACT_INDEX="register";
//    const DW_ACT_LOGIN="login";
//    const DW_ACT_LOGOUT="logout";
//    const DW_ACT_EXPORT_PROFILE="profile";
//    const DW_ACT_EXPORT_RESENDPWD="resendpwd";
//    const DW_ACT_EXPORT_ADMIN="admin";
//    const DW_ACT_RECOVER="recover";
//    const DW_ACT_DRAFT="draft";
//    const DW_ACT_WORDBLOCK="wordblock";
//    const DW_ACT_CONFLICT="conflict";
//    const DW_ACT_CANCEL="cancel";
//    const DW_ACT_DRAFTDEL="draftdel";

class ModelInterface {
    public function doFormatedPagePreProcess($pid){
            unlock($pid); //try to unlock        
    }
    
    private function doEditPagePreProcess($pid, $prev){
        if(in_array($pdo, array('edit', 'preview', 'recover'))) {
            $ACT = act_edit($ACT);
        }
    }
    
    
    public function getFormatedPageResponse($pid, $prev, $pageToSend=""){
        $pageToSend .= ModelInterface::getFormatedPage($pid, $prev);
        return ModelInterface::getContentPage($pid, $pageToSend);        
    }
    
    public function getLoginPageResponse(){
        return ModelInterface::getFormatedPageResponse("start", null);                
    }
    
    public function getLogoutPageResponse(){
        return array('id' => "logout_info",
        'title' => "desconectat",
        'content' => "Accés restringit. Per accedir cal que us identifiqueu");                
    }
    
    public function getCodePageResponse($pid, $pdo, $prev){
        $pageToSend = ModelInterface::getCodePage($pid, $pdo, $prev);
        return ModelInterface::getContentPage($pid, $pageToSend);        
    }
    
    private function getContentPage($pid, $pageToSend){
        $pageTitle = tpl_pagetitle($pid, true);
        $contentData = array('id' => \str_replace(":", "_",$pageTitle),
                                'title' => $pageTitle,
                                'content' => $pageToSend);
        return $contentData;                
    }
    
    private function getFormatedPage($pid, $prev){
        global $ID;
        global $REV;
        $old_ID = $ID;
        $old_REV = $REV;
        
        $ID = $pid;
        $REV = $prev;
        ob_start();
//        trigger_event('TPL_ACT_RENDER', $do, "tpl_content_core");
        trigger_event('TPL_ACT_RENDER', $ACT);
        html_show();
        $html_output = ob_get_clean()."\n";
        
        $ID = $old_ID;
        $REV = $old_REV;
        
        return $html_output;
    }

    private function getCodePage($pid, $pdo, $prev){
        ob_start();
        trigger_event('TPL_ACT_RENDER', $pdo, "tpl_content_core");
        $html_output = ob_get_clean()."\n";
        return $html_output;
    }
    
//    private function getContentPage(){
//        global $ID;
//        global $ACT;
//        global $REV;
//        
//        ob_start();
//        trigger_event('TPL_ACT_RENDER', $ACT, "tpl_content_core");
//        $html_output = ob_get_clean()."\n";
//        return $html_output;
//        
//    }
    
//    public function getCodePage($pid, $pdo, $prev){
//        global $ID;
//        global $ACT;
//        global $REV;
//        
//        $old_id = $ID;
//        $old_do = $ACT;
//        $old_rev = $REV;
//        
//        $ID  =  $pid;
//        $ACT =  $pdo;
//        $REV =  $rpev;
//        $ACT = act_edit($ACT);
//        // check permissions again - the action may have changed
//        $ACT = act_permcheck($ACT);
//        
//        $ret = ModelInterface::getContentPage();
//        $ID  =  $old_id;
//        $ACT =  $old_do;
//        $REV =  $old_rev;
//        return $ret;
//    }    
}

?>
