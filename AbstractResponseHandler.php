<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AbstractResponseHandler
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */

if (!defined("DOKU_INC")) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'ajaxcommand/AjaxCmdResponseGenerator.php');

abstract class AbstractResponseHandler {
    const LOGIN = 'login';
    const PAGE = 'page';
    const EDIT = 'edit';
    const CANCEL = 'cancel';
    const SAVE = 'save';

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
        $this->preResponse($requestParams, $ajaxCmdResponseGenerator);
        $this->response($requestParams, $responseData, $ajaxCmdResponseGenerator);
        $this->postResponse($requestParams, $responseData, $ajaxCmdResponseGenerator);        
    }
    
    protected abstract function response($requestParams, $responseData, 
                                                  &$ajaxCmdResponseGenerator);
    protected abstract function preResponse($requestParams, 
                                                  &$ajaxCmdResponseGenerator);    
    protected abstract function postResponse($requestParams, $responseData, 
                                                  &$ajaxCmdResponseGenerator);
}

?>
