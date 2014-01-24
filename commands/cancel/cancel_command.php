<?php
/**
 * Description of cancel_command
 *
 * @author Josep Cañellas
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once (DOKU_COMMAND.'AjaxCmdResponseGenerator.php');
require_once (DOKU_COMMAND.'JsonGenerator.php');
require_once(DOKU_COMMAND.'abstract_page_process_cmd.php');
//require_once (DOKU_COMMAND.'DokuModelWrapper.php');

class cancel_command extends abstract_page_process_cmd{

    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;

//        $defaultValues = array(
//            'id' => 'start',
////            'do' => 'draftdel',
//        );
//
//        $this->setParameters($defaultValues);        
    }
    
    public function getDwAct(){
        return DW_ACT_DRAFTDEL;
    }
    public function getDwId() {
        return $this->params['id'];
    }

    public function getDwRev() {
        return $this->params['rev'];
    }
    
    public function preprocess() {
        return $this->modelWrapper->doCancelEditPreprocess();
//        return $this->modelWrapper->doFormatedPagePreProcess();
    }

    protected function _run() {
        return $this->getResponse();
    }
    
    private function getResponse() {
        $ret=new AjaxCmdResponseGenerator();
        $contentData = $this->modelWrapper->getFormatedPageResponse();
 
        if($this->getResponseHandler()){
            $this->getResponseHandler()->processResponse($this->params, 
                                                        $contentData, $ret);
        }else{
            $ret->addHtmlDoc($contentData["id"], $contentData["ns"],
                    $contentData["title"], $contentData["content"]);
        }
        
        return $ret->getResponse();        
    }
}

?>
