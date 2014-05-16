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


class commandreport_command extends abstract_command_class{

    public function __construct() {
        
        parent::__construct();
        $this->authenticatedUsersOnly=false;
    }
    
    protected function process() {
        $response = "params: ";
        foreach ($this->params as $key => $value) {
            if(is_array($value)){
                if($value["error"]==0 
                                && is_uploaded_file($value["tmp_name"])){
                    $response .= $key."= {filename:".$value["name"];
                    $response .= ", type:".$value["type"];
                    $response .= ", content["
                             .file_get_contents($value["tmp_name"])."]}, ";
                }else{
                    $response .= $key."= ERROR(".$value["error"]."), ";
                }
            }else{
                $response .= $key."= ".$value.", ";
            }
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
