<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of rest_service
 *
 * @author josep
 */

require_once(dirname(__FILE__).'/abstract_command_class.php');
//require_once 'HTTP.php';

abstract class abstract_rest_command_class extends abstract_command_class {
    protected $supportedContentTypes;
    protected $supportedMethods;
    protected $defaultContentType='none';
  
    public function __construct() {
        parent::__construct();
        $this->types['method'] = abstract_command_class::T_STRING;
        $defaultValues = array('GET');
        $this->setParameters($defaultValues);
    }

    protected function setSupportedFormats(/*array*/ $supportedFormats) {
      $this->$supportedFormats = $supportedFormats;
    }

    protected function setSupportedMethods(/*array*/ $supportedMethods) {
      $this->supportedMethods = $supportedMethods;
    }

    public function bestContentType(){  
        $t=array();
        //$best = http_negotiate_content_type($this->supportedContentTypes, $t);
        return ((empty($t))?$this->defaultContentType:$best);
    }
    
    public function isContentTypeSupported(){
       return $this->bestContentType()!=='none';
     }

    public function dispatchRequest($method/*, $extra_url_params=NULL, $permission=NULL*/) {
        /*if($this->isAuthorized($permission)){*/
            if($this->isContentTypeSupported()){
                switch($method) {
                  case 'GET':
                    $ret = $this->processGet(/*$extra_url_params*/);
                    break;
                  case 'HEAD':
                    $ret = $this->processHead(/*$extra_url_params*/);
                    break;
                  case 'POST':
                    $ret = $this->processPost(/*$extra_url_params*/);
                    break;
                  case 'PUT':
                    $ret = $this->processPut(/*$extra_url_params*/);
                    break;
                  case 'DELETE':
                    $ret = $this->processDelete(/*$extra_url_params*/);
                    break;
                  default:
                    /* 501 (Not Implemented) for any unknown methods */
                    header('Allow: ' . implode($this->supportedMethods), true, 501);
                    $this->error=true;
                    $this->errorMessage="Error: ".$method." does not implemented"; /*TODO internacionalitzaió (convertir missatges en variable) */
                }
            }else{
                /* 406 Not Acceptable */
                header('406 Not Acceptable');  
                $this->error=true;
                $this->errorMessage="Error: Content type is not accepted"; /*TODO internacionalitzaió (convertir missatges en variable) */
            }
        /*}else{
            $this->error=true;
            $this->errorMessage="permission denied";
        }*/
        if($this->error && $this->throwsException){
            throw new Exception($this->errorMessage);
        }
        return $ret;
    }

    protected function methodNotAllowedResponse() {
      /* 405 (Method Not Allowed) */
      header('Allow: ' . implode($this->supportedMethods), true, 405);
    }

    public function processGet(/*$extra_url_params*/) {
      $this->methodNotAllowedResponse();
    }

    public function processHead(/*$extra_url_params*/) {
      $this->methodNotAllowedResponse();
    }

    public function processPost(/*$extra_url_params*/) {
      $this->methodNotAllowedResponse();
    }

    public function processPut(/*$extra_url_params*/) {
      $this->methodNotAllowedResponse();
    }

    public function processDelete(/*$extra_url_params*/) {
      $this->methodNotAllowedResponse();
    }
    
    protected function process() {
        $this->dispatchRequest($this->params['method']);
    }    
    
    protected function getDefaultResponse($response, &$responseGenerator) {
    }

    protected function getResponse(){
        return $this->process();
    }    
}

?>
