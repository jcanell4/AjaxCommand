<?php
/**
 * Description of page_command
 *
 * @author Josep Cañellas
 */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once (DOKU_COMMAND.'AjaxCmdResponseHandler.php');
require_once (DOKU_COMMAND.'JsonGenerator.php');
require_once(DOKU_COMMAND.'abstract_page_process_cmd.php');
require_once (DOKU_COMMAND.'DokuModelWrapper.php');

class page_command extends abstract_page_process_cmd{

    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
//        $this->types['idx'] = abstract_command_class::T_STRING;
        $this->types['rev'] = abstract_command_class::T_STRING;

        $defaultValues = array(
            'id' => 'start',
//            'do' => 'show',
        );

        $this->setParameters($defaultValues);        
    }
    
    public function getDwAct(){
        return DW_ACT_SHOW;
    }
    public function getDwId() {
        return $this->params['id'];
    }

    public function getDwRev() {
        return $this->params['rev'];
    }
    
    public function preprocess() {
        return $this->modelWrapper->doFormatedPagePreProcess();
    }

    protected function _run() {
        return $this->getResponse();
    }
    
    private function getResponse() {
        global $conf;
        $ret=new ArrayJSonGenerator();
        $contentData = $this->modelWrapper->getFormatedPageResponse();
        $ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
                  array("type" => ResponseGenerator::JSINFO,
		    "value" => $this->modelWrapper->getJsInfo(),
                )));                  
        
        $ret->add(new ResponseGenerator(ResponseGenerator::HTML_TYPE, 
                $contentData));
        
        $metaData = $this->modelWrapper->getMetaResponse();
        $ret->add(new ResponseGenerator(ResponseGenerator::HTML_TYPE, 
                $metaData));
        $ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
                  array("type" => ResponseGenerator::PROCESS_DOM_FROM_FUNCTION,
		    "id" => $contentData['id'], 
                    "amd" => true,
                    "processName" => "ioc/dokuwiki/processContentPage",
                    "params" => array(
                        "ns" => $contentData['title'], 
                        "command" => "lib/plugins/ajaxcommand/ajax.php?call=edit",
                     ))));                  
        return $ret->getJsonEncoded();        
    }
}

?>
