<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ResponseHandler
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */

if (!defined("DOKU_INC")) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'ajaxcommand/AjaxCmdResponseGenerator.php');

abstract class ResponseHandler {
    const LOGIN = 'login';
    const PAGE = 'page';
    const EDIT = 'edit';
    const CANCEL = 'cancel';

    private $cmd;
    
    public function __construct($cmd) {
        $this->cmd=$cmd;
    }
    
    public function getCommandName(){
        return $this->cmd;
    }
    
    public function processResponse($requestParams, 
                                            $responseData, 
                                            &$ajaxCmdResponseGenerator){
        $this->preProcess($requestParams, $ajaxCmdResponseGenerator);
        $this->process($requestParams, $responseData, $ajaxCmdResponseGenerator);
        $this->postProcess($requestParams, $responseData, $ajaxCmdResponseGenerator);        
    }
    
    protected abstract function process($requestParams, $responseData, 
                                                  &$ajaxCmdResponseGenerator);
    protected abstract function preProcess($requestParams, 
                                                  &$ajaxCmdResponseGenerator);    
    protected abstract function postProcess($requestParams, $responseData, 
                                                  &$ajaxCmdResponseGenerator);
}

?>
