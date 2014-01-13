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

class edit_command extends abstract_page_process_cmd{

    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
//        $this->types['idx'] = abstract_command_class::T_STRING;
        $this->types['do'] = abstract_command_class::T_STRING;
        $this->types['rev'] = abstract_command_class::T_STRING;
        $this->types['range'] = abstract_command_class::T_STRING;
        $this->types['date'] = abstract_command_class::T_STRING; 

        $defaultValues = array(
            'id' => 'index',
            'do' => 'edit',
        );

        $this->setParameters($defaultValues);        
    }
    
    public function getDwAct(){
        return $this->params['do'];
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

    protected function preprocess() {
        $this->modelWrapper->doEditPagePreProcess();
    }

    protected function _run() {
        return $this->getResponse();
    }
    
    private function getResponse() {
        global $conf;
        $ret=new AjaxCmdResponseHandler();
        $contentData = $this->modelWrapper->getCodePageResponse(
                                                    $this->params['do'],
                                                    $this->params['id'],
                                                    $this->params['rev'],
                                                    $this->params['range'],
                                                    $this->content);
        $ret->addWikiCodeDoc($contentData);
        $ret->addProcessFunction(true, "ioc/dokuwiki/processEditing", 
                                $this->modelWrapper->getToolbarIds());
//        $ret->add(new ResponseGenerator(ResponseGenerator::DATA_TYPE, 
//                $contentData));
//        $ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
//                  array("type" => ResponseGenerator::PROCESS_FUNCTION,
//		    "amd" => true,
//                    "processName" => "ioc/dokuwiki/ace-main",
//                    )));   
        return $ret->getResponse();        
    }
}

?>
