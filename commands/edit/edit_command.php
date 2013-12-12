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

class edit_command extends abstract_command_class{

    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
        $this->types['idx'] = abstract_command_class::T_STRING;
        $this->types['do'] = abstract_command_class::T_STRING;
        $this->types['rev'] = abstract_command_class::T_STRING;
        $this->types['range'] = abstract_command_class::T_STRING;

        $defaultValues = array(
            'id' => 'index',
            'do' => 'edit',
        );

        $this->setParameters($defaultValues);        
    }
    
    public function getDokuwikiAct(){
        return $this->params['do'];
    }
    
    public function preprocess() {
        $this->modelInterface->doEditPagePreProcess($this->params['do']
                                                ,$this->params['id']
                                                ,$this->params['rev']
                                                ,$this->params['range']);
    }

    protected function _run() {
        return $this->getResponse();
    }
    
    private function getResponse() {
        global $conf;
        $ret=new ArrayJSonGenerator();
        $contentData = $this->modelInterface->getCodePageResponse(
                                                    $this->params['do'],
                                                    $this->params['id'],
                                                    $this->params['rev'],
                                                    $this->params['range'],
                                                    $this->content);
//        $pageTitle = $contentData['title'];
//        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::TITLE_TYPE,
//                $pageTitle." - ".hsc($conf["title"])));
        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::DATA_TYPE, 
                $contentData));
        return $ret->getJsonEncoded();        
    }
}

?>
