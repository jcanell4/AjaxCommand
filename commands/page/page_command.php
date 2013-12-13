<?php
/**
 * Description of page_command
 *
 * @author Josep Cañellas
 */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once (DOKU_COMMAND.'JsonGenerator.php');
require_once(DOKU_COMMAND.'abstract_command_class.php');
require_once (DOKU_COMMAND.'ModelInterface.php');

class page_command extends abstract_command_class{

    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
        $this->types['idx'] = abstract_command_class::T_STRING;
        $this->types['rev'] = abstract_command_class::T_STRING;

        $defaultValues = array(
            'id' => 'index',
//            'do' => 'show',
        );

        $this->setParameters($defaultValues);        
    }
    
    public function getDokuwikiAct(){
        return DW_ACT_SHOW;
    }
    
    public function preprocess() {
        $this->modelInterface->doFormatedPagePreProcess($this->params['id']);
    }

    protected function _run() {
        return $this->getResponse();
    }
    
    private function getResponse() {
        global $conf;
        $ret=new ArrayJSonGenerator();
        $contentData = $this->modelInterface->getFormatedPageResponse(
                                                    $this->params['id'],
                                                    $this->params['rev'],
                                                    $this->content);
//        $pageTitle = $contentData['title'];
//        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::TITLE_TYPE,
//                $pageTitle." - ".hsc($conf["title"])));
        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::HTML_TYPE, 
                $contentData));
        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
                  array("type" => BasicJsonGenerator::PROCESS_DOM_FROM_FUNCTION,
		    "id" => $contentData['id'], "amd" => true,
                    "processName" => "ioc/dokuwiki/processContentPage",
                    "params" => "lib/plugins/ajaxcommand/ajax.php?call=edit")));                  
        return $ret->getJsonEncoded();        
    }
}

?>
