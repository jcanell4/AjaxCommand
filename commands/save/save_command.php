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
require_once(DOKU_COMMAND.'abstract_command_class.php');
//require_once (DOKU_COMMAND.'DokuModelWrapper.php');

class save_command extends abstract_command_class{

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
    
    protected function _run() {
        $ret = $this->modelWrapper->saveEdition(
                        $this->params['id'], $this->params['rev'], 
                        $this->params['range'], $this->params['date'], 
                        $this->params['prefix'], $this->params['wikitext'], 
                        $this->params['suffix'], $this->params['summary']                
        );
        return $ret;
    }
    
    protected function getDefaultResponse($response, &$ret) {
        $ret->addWikiCodeDoc($contentData["id"], $contentData["ns"],
                    $contentData["title"], $contentData["content"]);
    }
}

?>
