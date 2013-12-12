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
require_once DOKU_INC.'inc/actions.php';


if(!defined('DW_ACT_SHOW')) define('DW_ACT_SHOW',"show");
if(!defined('DW_ACT_EDIT')) define('DW_ACT_EDIT',"edit");
if(!defined('DW_ACT_PREVIEW')) define('DW_ACT_PREVIEW',"preview");
if(!defined('DW_ACT_RECOVER')) define('DW_ACT_RECOVER',"recover");
if(!defined('DW_ACT_DENIED')) define('DW_ACT_DENIED',"denied");

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
//    const DW_ACT_DRAFT="draft";
//    const DW_ACT_WORDBLOCK="wordblock";
//    const DW_ACT_CONFLICT="conflict";
//    const DW_ACT_CANCEL="cancel";
//    const DW_ACT_DRAFTDEL="draftdel";



function onFormatRender($data){
    html_show();
}

function onCodeRender($data){
    switch($data){
            case 'edit':
            case 'recover':
                html_edit();
                break;
            case 'preview':
                html_edit();
                html_show($TEXT);
                break; 
            case 'denied':
                print p_locale_xhtml('denied');
                break;            
    }
}

class ModelInterface {
    protected $params;
    
    public function runPreprocess($command){
        global $ACT;
        
        $brun=false;
        if($command->getDokuwikiAct()){
            // give plugins an opportunity to process the action
            $ACT = $command->getDokuwikiAct();
            $evt = new Doku_Event('ACTION_ACT_PREPROCESS', $ACT);                
            ob_start();
            $brun = ($evt->advise_before());
            $command->content = ob_get_clean();
        }
        if(!$command->getDokuwikiAct() || $brun){
            $command->preprocess();
        }
        if($command->getDokuwikiAct()){
            ob_start();
            $evt->advise_after();
            $command->content .= ob_get_clean();
            unset($evt);
        }
    }
    
    public function doFormatedPagePreProcess($pid){
        $this->params['id'] = $pid;
        unlock($pid); //try to unlock        
    }
    
    private function doEditPagePreProcess($pdo, $pid, $prev=NULL, $prange=NULL){
        global $ID;
        global $ACT;
        global $RANGE;
        global $REV;
    
        if(in_array($pdo, array(DW_ACT_EDIT, DW_ACT_PREVIEW, DW_ACT_RECOVER))) {
            $REV =  $this->params['rev'] = $prev;
            $RANGE =  $this->params['range'] = $prange;
            $ID=   $this->params['id'] = $pid;
            $ACT = $this->params['do']=$pdo;
            $ACT = act_edit($ACT);
            // check permissions again - the action may have changed
            $ACT = act_permcheck($ACT);            
        }
    }
    
    
    public function getFormatedPageResponse($pid, $prev=NULL, $pageToSend=""){
        global $ID;
        global $ACT;
        global $REV;

        $REV =  $this->params['rev'] = $prev;
        $ID=   $this->params['id'] = $pid;
        $ACT = DW_ACT_SHOW;
        $pageToSend .= $this->getFormatedPage();
        return $this->getContentPage($pageToSend);        
    }
    
    public function getLoginPageResponse(){
        return $this->getFormatedPageResponse("start");                
    }
    
    public function getLogoutPageResponse(){
        return array('id' => "logout_info",
        'title' => "desconectat",
        'content' => "Accés restringit. Per accedir cal que us identifiqueu");                
    }
    
    public function getCodePageResponse($pdo, $pid, $prev=NULL, $prange=NULL,
                                        $pageToSend=""){
        global $ID;
        global $ACT;
        global $RANGE;
        global $REV;

        $REV =  $this->params['rev'] = $prev;
        $RANGE =  $this->params['range'] = $prange;
        $ID=   $this->params['id'] = $pid;
        $ACT = $this->params['do']=$pdo;
        
        $pageToSend .= $this->getCodePage();
        return $this->getContentPage($pageToSend);        
    }
    
    public function isDenied(){
        return $this->params['do']==DW_ACT_DENIED;
    }


    private function getContentPage($pageToSend){
        $pageTitle = tpl_pagetitle($this->params['id'], true);
        $contentData = array('id' => \str_replace(":", "_",$pageTitle),
                                'title' => $pageTitle,
                                'content' => $pageToSend);
        return $contentData;                
    }
    
    private function getFormatedPage(){
        ob_start();
//        trigger_event('TPL_ACT_RENDER', $do, "tpl_content_core");
        trigger_event('TPL_ACT_RENDER', $ACT, 'onFormatRender');
        $html_output = ob_get_clean()."\n";
        return $html_output;
    }

    private function getCodePage(){
        ob_start();
        trigger_event('TPL_ACT_RENDER', $ACT, "onCodeRender");
        $html_output = ob_get_clean()."\n";
        return $html_output;
    }
}
?>
