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
    //put your code here
    public function getContentPageResponse($pid, $pdo, $prev){
        global $conf;
        $pageToSend = ModelInterface::getContentPage($pid, $pdo, $prev);
        $pageTitle = tpl_pagetitle($pid, true);
        $contentData = array('id' => \str_replace(":", "_",$pageTitle),
                                'title' => $pageTitle,
                                'content' => $pageToSend);
        return $contentData;        
    }
    
    public function getLoginPageResponse(){
        return ModelInterface::getContentPageResponse("start", "show", null);                
    }
    
    public function getLogoutPageResponse(){
        return array('id' => "logout_info",
        'title' => "desconectat",
        'content' => "Accés restringit. Per accedir cal que us identifiqueu");                
    }
    
    private function getContentPage($pid, $pdo, $prev){
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
    
    public function getFormatedPage($pid, $prev){
        return ModelInterface::getContentPage($pid, 
                                                DW_ACT_SHOW, 
                                                $prev);
    }
    
    public function getCodePage($pid, $pdo, $prev){
        return ModelInterface::getContentPage($pid, 
                                                DW_ACT_EDIT, 
                                                $prev);
    }    
}

?>
