<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DokuModelAdapter
 *
 * @author professor
 */
if(!defined('DOKU_INC')) die();
//require common
require_once DOKU_INC.'inc/actions.php';
require_once(DOKU_COMMAND.'WikiIocModel.php');


if(!defined('DW_DEFAULT_PAGE')) define('DW_DEFAULT_PAGE',"start");
if(!defined('DW_ACT_SHOW')) define('DW_ACT_SHOW',"show");
if(!defined('DW_ACT_DRAFTDEL')) define('DW_ACT_DRAFTDEL',"draftdel");
if(!defined('DW_ACT_DRAFTDEL')) define('DW_ACT_SAVE',"save");
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
    global $TEXT;
    
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

function wrapper_tpl_toc(){
    $toc = tpl_toc(true);
    $toc = preg_replace('/(<!-- TOC START -->\s?)(.*\s?)(<div class=.*tocheader.*<\/div>|<h3 class=.*toggle.*<\/h3>)((.*\s)*)(<!-- TOC END -->)/i', 
                        '$1<div class="dokuwiki">$2$4</div>$6', $toc);
    return $toc;
}

class DokuModelAdapter implements WikiIocModel{
    protected $params;
    protected $dataTmp;
    protected $ppEvt;
    
    public function getHtmlPage($pid, $prev=NULL){
        $this->startPageProcess(DW_ACT_SHOW, $pid, $prev);
        $this->doFormatedPagePreProcess();
        return $this->getFormatedPageResponse();
    }
    
    public function getCodePage($pdo, $pid, $prev=NULL, $prange=NULL){
         $this->startPageProcess($pdo, $pid, $prev, $prange);
         $this->doEditPagePreProcess();
         return $this->getCodePageResponse();
    }
    
    public function cancelEdition($pid, $prev=NULL){
        $this->startPageProcess(DW_ACT_DRAFTDEL, $pid, $prev);
        $this->doCancelEditPreprocess();
        return $this->getFormatedPageResponse();
    }
    
    public function saveEdition($pid, $prev=NULL, $prange=NULL, 
                $pdate=NULL, $ppre=NULL, $ptext=NULL, $psuf=null, $psum=NULL){
        $this->startPageProcess(DW_ACT_SAVE, $pid, $prev, $prange, $pdate, 
                                $ppre, $ptext, $psuf, $psum);
        $this->doSavePreProcess();
        $this->doEditPagePreProcess();
        return $this->getCodePageResponse();
    }
    
    public function isDenied(){
        return $this->params['do']==DW_ACT_DENIED;
    }
    
    public function getNsTree($currentnode, $sortBy){
        global $conf;
        $sortOptions=array(0 => 'name', 'date');
        $tree = array();
        $tree_json=  array();
        $strData;
        $json = new JSON();
        
        if($currentnode=="_"){
            return $json->enc(array('id' => "", 'name' => "", 'type' => 'd'));
            
        }
        if($currentnode){
            $node = $currentnode;
            $aname = split(":", $currentnode);
            $level = count($aname);
            $name = $aname[$level-1];
        }else{
            $node = '';
            $name = '';
            $level=0;
        }
        $sort=$sortOptions[$sortBy];
        $base=$conf['datadir'];
        
        $opts = array('ns' => $node);
        $dir = str_replace(':', '/', $node);
        search($tree, $base, 'search_index', 
                    $opts, $dir, 1);
        foreach(array_keys($tree) as $item){
            $tree_json[$item]['id'] = $tree[$item]['id'] ;
            $aname = split(":", $tree[$item]['id']);
            $tree_json[$item]['name'] = $aname[$level];
            $tree_json[$item]['type'] = $tree[$item]['type'];
        }
        
        $strData = $json->enc(array('id' => $node, 'name' => $node, 
                                'type' => 'd', 'children' => $tree_json));
//        $strData = $json->enc($tree);
        return $strData;              
    }

    /**
     * Inicia tractament d'una pàgina de la dokuwiki
     */
    private function startPageProcess($pdo, $pid=NULL, $prev=NULL, $prange=NULL, 
                $pdate=NULL, $ppre=NULL, $ptext=NULL, $psuf=null, $psum=NULL){
        global $ID;
        global $ACT;
        global $REV;
        global $RANGE;
        global $DATE;
        global $PRE;
        global $TEXT;
        global $SUF;
        global $SUM;
        
        $ACT = $this->params['do'] = $pdo;
        if(!$pid){
            $pid=DW_DEFAULT_PAGE;
        }
        $ID = $this->params['id'] = $pid;
        if($prev){
            $REV = $this->params['rev'] = $prev;
        }
        if($prange){
            $RANGE = $this->params['range'] = $prange;
        }
        if($pdate){
            $DATE = $this->params['date'] = $pdate;
        }
        if($ppre){
            $PRE = $this->params['pre'] = cleanText(substr($ppre, 0, -1));
        }
        if($ptext){            
            $TEXT = $this->params['text'] = cleanText($ptext);            
        }
        if($psuf){
            $SUF = $this->params['suf'] = cleanText($psuf);
        }
        if($psum){
            $SUM = $this->params['sum'] = $psum;
        }
        
        $this->fillInfo();
        
//        trigger_event('DOKUWIKI_STARTED',  $this->dataTmp);
//        trigger_event('WIOC_AJAX_COMMAND_STARTED',  $this->dataTmp);
    }
    
    /**
     * Realitza el per-procés d'una pàgina de la dokuwiki en format HTML. 
     * Permet afegir etiquetes HTML al contingut final durant la fase de 
     * preprocés 
     * @return string 
     */
    private function doFormatedPagePreProcess(){
        $content = "";
        if($this->runBeforePreprocess($content)){
            unlock($this->params['id']); //try to unlock   
        }
        $this->runAfterPreprocess($content);
        return $content;
    }
    
    private function doEditPagePreProcess(){
        global $ACT;
        
        $content = "";
        if($this->runBeforePreprocess($content)){
            $ACT = act_edit($ACT);
            // check permissions again - the action may have changed
            $ACT = act_permcheck($ACT);            
        }
        $this->runAfterPreprocess($content);
        return $content;        
    }
    
    private function doSavePreProcess(){
        global $ACT;
        
        act_save($ACT);
        $ACT = $this->params['do'] = "edit";
        $this->doEditPagePreProcess();
    }
    
    private function doCancelEditPreProcess(){
        global $ACT;
        
        $ACT = act_draftdel($ACT);
        $this->doFormatedPagePreProcess();
    }
    
    private function getFormatedPageResponse(){
        $id = $this->params['id'];
        $pageTitle = tpl_pagetitle($this->params['id'], true);
        $pageToSend = $this->getFormatedPage();
        return $this->getContentPage($pageToSend);        
    }
    
    private function getCodePageResponse(){
        $pageToSend = $this->_getCodePage();
        return $this->getContentPage($pageToSend);        
    }
    
    private function getMetaResponse(){
        global $lang;
        $ret=array('docId' => \str_replace(":", "_",$this->params['id']));
        $meta=array();
        $mEvt = new Doku_Event('WIOC_ADD_META', $meta);                
        if($mEvt ->advise_before()){
            $toc = wrapper_tpl_toc();
            $metaId = \str_replace(":", "_",$this->params['id']).'_toc';
            $meta[] = $this->getMetaPage($metaId, $lang['toc'], $toc);
        }       
        $mEvt->advise_after();
        unset($mEvt);        
        $ret['meta']=$meta;
        return $ret;        
    }
    
    private function getJsInfo(){
        global $JSINFO;
        $this->fillInfo();
        return $JSINFO;                        
    }
    
    private function getToolbarIds(){
        return array("varName" => "toolbar", 
                    "toolbarId" => "tool__bar",
                    "wikiTextId" => "wiki__text");
    }
    
    private function runBeforePreprocess(&$content){
        global $ACT;
        
        $brun=false;
        // give plugins an opportunity to process the action
        $this->ppEvt = new Doku_Event('ACTION_ACT_PREPROCESS', $ACT);                
        ob_start();
        $brun = ($this->ppEvt->advise_before());
        $content = ob_get_clean();
        return $brun;
    }

    private function runAfterPreprocess(&$content){
        ob_start();
        $this->ppEvt->advise_after();
        $content .= ob_get_clean();
        unset($this->ppEvt);
    }
    
    private function fillInfo(){
        global $INFO;
        global $JSINFO;
        global $ID;
        
        $INFO = pageinfo();
        //export minimal infos to JS, plugins can add more
        $JSINFO['id']        = $ID;
        $JSINFO['namespace'] = (string) $INFO['namespace']; 
    }
   private function getContentPage($pageToSend){
        $pageTitle = tpl_pagetitle($this->params['id'], true);
        $contentData = array('id' => \str_replace(":", "_",$this->params['id']),
                                'ns' => $this->params['id'],
                                'title' => $pageTitle,
                                'content' => $pageToSend);
        return $contentData;                
    }
    
    private function getMetaPage($metaId, $metaTitle, $metaToSend){
        $contentData = array('id' => $metaId,
            'title' => $metaTitle,
            'content' => $metaToSend);
        return $contentData;                
    }
    
    
    private function getFormatedPage(){
        global $ACT;
        
        ob_start();
//        trigger_event('TPL_ACT_RENDER', $do, "tpl_content_core");
        trigger_event('TPL_ACT_RENDER', $ACT, 'onFormatRender');
        $html_output = ob_get_clean()."\n";
        return $html_output;
    }

    private function _getCodePage(){
        global $ACT;

        ob_start();
        trigger_event('TPL_ACT_RENDER', $ACT, 'onCodeRender');
        $html_output = ob_get_clean()."\n";
        return $html_output;
    }    
}
?>
