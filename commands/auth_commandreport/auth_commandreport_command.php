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
require_once(DOKU_COMMAND.'abstract_command_class.php');


class auth_commandreport_command extends abstract_command_class{

    public function __construct() {
        
        parent::__construct();
        $this->authenticatedUsersOnly=true;
    }
    
    protected function process() {
        $response = "params: ";
        foreach ($this->params as $key => $value) {
            $response .= $key.": ".$value.", ";
        }
        $response = substr($response, 0, -2);
        
        return $response;
    }

    protected function preprocess() {}

    protected function startCommand() {}

    protected function getDefaultResponse($response, &$ret) {
        $ret->addInfoDta($response);        
    }
}

?>
