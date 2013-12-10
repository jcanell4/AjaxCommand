<?php

/**
 * Description of abstract_command_class
 *
 * @author professor
 */
require_once(dirname(__FILE__).'/ModelInterface.php');

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
    
    protected $params = array();
    protected $types = array();
    protected $permissionFor = array();
    protected $authenticatedUsersOnly=true;
    var $error = false;
    var $errorMessage = '';
    var $throwsException=false;
    
    public function __construct() {}

    public function setThrowsException($onoff){
        $this->throwsException=$onoff;
    }
    
    protected function setParameterTypes($types){
        $this->types=$types;
    }
    
    protected function setParameterDefaultValues($defaultValue){
        $this->setParameters($defaultValue);
    }
    
    abstract protected function getDokuwikiAct();

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
        $brun=false;
        if(!$this->authenticatedUsersOnly 
                || $this->isSecurityTokenVerified()
                && $this->isUserAuthenticated()
                && $this->isAuthorized($permission)){
            if($this->getDokuwikiAct()){
                // give plugins an opportunity to process the action
                $evt = new Doku_Event('ACTION_ACT_PREPROCESS',
                                            $this->getDokuwikiAct());                
                $brun = ($evt->advise_before());
            }
            if(!$this->getDokuwikiAct() || $brun){
                $ret = $this->_run();
            }
            if($this->getDokuwikiAct()){
                $evt->advise_after();
                unset($evt);
            }
        }else{
            $this->error=true;
            $this->errorMessage="permission denied"; /*TODO internacionalització */
        }
        if($this->error && $this->throwsException){
            throw new Exception($this->errorMessage);
        }
        return $ret;
    }
    
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
    
}

?>
