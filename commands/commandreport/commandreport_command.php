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
require_once(DOKU_COMMAND.'JsonGenerator.php');
require_once(DOKU_COMMAND.'abstract_command_class.php');
require_once (DOKU_COMMAND.'DokuModelWrapper.php');


class commandreport_command extends abstract_command_class{

    public function __construct() {
        
        parent::__construct();
        $this->authenticatedUsersOnly=false;
    }
    
    //tpl_content(((tpl_getConf("vector_toc_position") === "article") ? true : false));
    protected function _run() {
        global $conf;
        $ret=new ArrayJSonGenerator();
        $response = "params: ";
        foreach ($this->params as $key => $value) {
            $response .= $key.": ".$value.", ";
        }
        $response = substr($response, 0, -2);
        
        $ret->add(new ResponseGenerator(ResponseGenerator::INFO_TYPE, 
                $response));
        return $ret->getJsonEncoded();
    }

    protected function preprocess() {
        
    }

    protected function startCommand() {
        
    }
}

?>
