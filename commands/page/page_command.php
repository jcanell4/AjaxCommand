<?php
/**
 * Description of page_command
 *
 * @author Josep Cañellas
 */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once(DOKU_COMMAND.'abstract_command_class.php');
require_once (DOKU_COMMAND.'ModelInterface.php');

class page_command extends abstract_command_class{

    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
        $this->types['idx'] = abstract_command_class::T_STRING;
        $this->types['do'] = abstract_command_class::T_STRING;
        $this->types['rev'] = abstract_command_class::T_STRING;

        $defaultValues = array(
            'id' => 'index',
            'do' => 'show',
        );

        $this->setParameters($defaultValues);        
    }
    
    //tpl_content(((tpl_getConf("vector_toc_position") === "article") ? true : false));
    protected function _run() {
        return $this->getResponse();
    }
    
    private function getResponse() {
        global $conf;
        $ret=new ArrayJSonGenerator();
        $contentData = ModelInterface::getContentPageResponse(
                                                    $this->params['id'],
                                                    $this->params['do'],
                                                    $this->params['rev']);
        $pageTitle = $contentData['title'];
        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::TITLE_TYPE,
                $pageTitle." - ".hsc($conf["title"])));
        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::DATA_TYPE, 
                $contentData));
        return $ret->getJsonEncoded();        
    }
}

?>
