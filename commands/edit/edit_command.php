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
    private $old_id;
    private $old_do;
    private $old_rev;

    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
        $this->types['idx'] = abstract_command_class::T_STRING;
        $this->types['do'] = abstract_command_class::T_STRING;
        $this->types['rev'] = abstract_command_class::T_STRING;
        $this->types['range'] = abstract_command_class::T_STRING;

        $defaultValues = array(
            'id' => 'index',
            'do' => 'show',
        );

        $this->setParameters($defaultValues);        
    }
    
    public function getDokuwikiAct(){
        return $this->params['do'];
    }
    
    protected function _start() {
        global $ID;
        global $ACT;
        global $REV;
        
        $this->old_id = $ID;
        $this->old_do = $ACT;
        $this->old_rev = $REV;
        
        $ID = $this->params['id'];
        $ACT = $this->params['do'];
        $REV = $this->params['rev'];
    }
    
    public function preprocess() {
        $this->modelInterface->doFormatedPagePreProcess($this->params['id']);
    }

    protected function _run() {
        return $this->getResponse();
    }
    
    protected function _finish() {
        $ID = $this->old_id;
        $ACT = $this->old_do;
        $REV = $this->old_rev;
    }
    
    
    private function getResponse() {
        global $conf;
        $ret=new ArrayJSonGenerator();
        $contentData = $this->modelInterface->getCodePage(
                                                    $this->params['id'],
                                                    $this->params['rev'],
                                                    $this->params['range'],
                                                    $this->content);
        $pageTitle = $contentData['title'];
        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::TITLE_TYPE,
                $pageTitle." - ".hsc($conf["title"])));
        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::DATA_TYPE, 
                $contentData));
        return $ret->getJsonEncoded();        
    }
}

?>
