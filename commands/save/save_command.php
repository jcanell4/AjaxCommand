<?php
/**
 * Description 
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

class save_command extends abstract_page_process_cmd{

    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
        $this->types['rev'] = abstract_command_class::T_STRING;
        $this->types['range'] = abstract_command_class::T_STRING;
        $this->types['date'] = abstract_command_class::T_STRING; 
        $this->types['prefix'] = abstract_command_class::T_STRING; 
        $this->types['suffix'] = abstract_command_class::T_STRING; 
        $this->types['changecheck'] = abstract_command_class::T_STRING; 
        $this->types['target'] = abstract_command_class::T_STRING; 
        $this->types['summary'] = abstract_command_class::T_STRING; 

        $defaultValues = array(
            'id' => 'index',
        );

        $this->setParameters($defaultValues);        
    }
    
    public function getDwAct(){
        return DW_ACT_SAVE;
    }
    
    public function getDwId() {
        return $this->params['id'];
    }

    public function getDwRev() {
        return $this->params['rev'];
    }

    public function getDwRange() {
        return $this->params['range'];
    }
    
    public function getDwDate() {
        return $this->params['date'];
    }

    public function getDwPre() {
        return $this->params['prefix'];
    }
    
    public function getDwSuf() {
        return $this->params['suffix'];
    }
    
    public function getDwSum() {
        return $this->params['summary'];
    }
    
    public function getDwText() {
        return $this->params['wikitext'];
    }

    protected function preprocess() {
        $this->modelWrapper->doSavePreProcess();
    }

    protected function _run() {
        return $this->getResponse();
    }
    
    private function getResponse() {
        global $conf;
        $ret=new AjaxCmdResponseGenerator();
        $contentData = $this->modelWrapper->getCodePageResponse();
        if($this->getResponseHandler()){
            $this->getResponseHandler()->processResponse($this->params, 
                                                        $contentData, $ret);
        }else{
            $ret->addWikiCodeDoc($contentData["id"], $contentData["ns"],
                    $contentData["title"], $contentData["content"]);
        }
        
        return $ret->getResponse();        
    }
}

?>
