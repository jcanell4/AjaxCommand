<?php

/**
 * Description of abstract_command_class
 *
 * @author professor
 */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'ajaxcommand/DokuModelAdapter.php');
require_once(DOKU_PLUGIN.'ajaxcommand/ResponseHandler.php');

abstract class abstract_command_class {
    const T_BOOLEAN = "boolean";
    const T_INTEGER = "integer";
    const T_DOUBLE = "double";
    const T_FLOAT = "float";
    const T_STRING = "string";
    const T_ARRAY = "array";
    const T_OBJECT = "object";
    const T_FUNCTION = "function";
    const T_METHOD = "method";

    protected $responseHandler=NULL;

    protected $params = array();
    protected $types = array();
    protected $permissionFor = array();
    protected $authenticatedUsersOnly=true;
    protected $runPreprocess=false;
    protected $modelWrapper;
    

    var $content='';
    var $error = false;
    var $errorMessage = '';
    var $throwsException=false;
    
    public function __construct() {
        $this->modelWrapper = new DokuModelAdapter();
    }
    
    public function setResponseHandler($respHand){
        $this->responseHandler=$respHand;
    }
    
    public function getResponseHandler(){
        return $this->responseHandler;
    }

    public function setThrowsException($onoff){
        $this->throwsException=$onoff;
    }
    
    protected function setParameterTypes($types){
        $this->types=$types;
    }
    
    protected function setParameterDefaultValues($defaultValue){
        $this->setParameters($defaultValue);
    }
    
    public function getDwAct(){
        return "";
    }
    
    public function getDwId(){
        return "";
    }
    
    public function getDwRev(){
        return NULL;
    }
    
    public function getDwRange(){
        return NULL;
    }
    
    public function getDwDate(){
        return NULL;
    }
    
    public function getDwPre(){
        return NULL;
    }
    
    public function getDwText(){
        return NULL;
    }
    
    public function getDwSuf(){
        return NULL;
    }
    
    public function getDwSum(){
        return NULL;
    }
    
    public function setParameters($params){
        foreach ($params as $key => $value){
            if(isset($this->types[$key]) 
                    && gettype($value)!=$this->types[$key]){
               settype($value, $this->types[$key]);
            }
            $this->params[$key] = $value;
        }
    }
    
    public function run($permission=NULL){
        if(!$this->authenticatedUsersOnly 
                || $this->isSecurityTokenVerified()
                && $this->isUserAuthenticated()
                && $this->isAuthorized($permission)){
            
            $ret="";
            $this->startCommand();
            $ret .= $this->preprocess();
            $ret .= $this->_run();  
            
//            $this->_finish();
            if($this->modelWrapper->isDenied()){
                $this->error=true;
                $this->errorMessage="permission denied"; /*TODO internacionalització */
            }
        }else{
            $this->error=true;
            $this->errorMessage="permission denied"; /*TODO internacionalització */
        }
        if($this->error && $this->throwsException){
            throw new Exception($this->errorMessage);
        }
        return $ret ;
    }
    
    abstract protected function startCommand();

    abstract protected function preprocess();

    protected function isUserAuthenticated(){
        global $_SERVER;
        return $_SERVER['REMOTE_USER']?true:false;
    }
    
    protected function isSecurityTokenVerified(){
        return checkSecurityToken();
    }

    protected function  isAuthorized($permission){
        $found = sizeof($this->permissionFor)==0 || !is_array($permission);
        for($i=0; !$found && $i<sizeof($permission); $i++){
            $found = in_array($permission[$i], $this->permissionFor);
        }
        return $found;
    }

    protected abstract function _run();
//    protected function _finish(){        
//    }
}

?>
