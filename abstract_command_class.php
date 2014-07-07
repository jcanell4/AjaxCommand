<?php

/**
 * Description of abstract_command_class
 *
 * @author professor
 */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'ajaxcommand/DokuModelAdapter.php');
require_once(DOKU_PLUGIN.'ajaxcommand/AbstractResponseHandler.php');
require_once(DOKU_INC.'inc/plugin.php');


abstract class abstract_command_class extends DokuWiki_Plugin{
    const T_BOOLEAN = "boolean";
    const T_INTEGER = "integer";
    const T_DOUBLE = "double";
    const T_FLOAT = "float";
    const T_STRING = "string";
    const T_ARRAY = "array";
    const T_OBJECT = "object";
    const T_FUNCTION = "function";
    const T_METHOD = "method";
    const T_FILE = "file";
    
    protected static $PLUGUIN_TYPE='command';
    protected static $FILENAME_PARAM='name';
    protected static $FILE_TYPE_PARAM='type';
    protected static $ERROR_PARAM='error';
    protected static $FILE_CONTENT_PARAM='tmp_name';

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
            
            $ret = $this->getResponse();  
            
            
            if($this->modelWrapper->isDenied()){
                $this->error=403;
                $this->errorMessage="permission denied"; /*TODO internacionalització */
            }
        }else{
            $this->error=403;
            $this->errorMessage="permission denied"; /*TODO internacionalització */
        }
        if($this->error && $this->throwsException){
            throw new Exception($this->errorMessage);
        }
        return $ret ;
    }
    
    protected function getResponse(){
        $ret=new AjaxCmdResponseGenerator();
        $response = $this->process();
 
        if($this->getResponseHandler()){
            $this->getResponseHandler()->processResponse($this->params, 
                                                        $response, $ret);
        }else{
            $this->getDefaultResponse($response, $ret);
        }
        
        return $ret->getResponse();        
        
    }
    
    protected abstract function getDefaultResponse($response, &$responseGenerator);

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
    
    public function getPluginType() {
        return self::$PLUGUIN_TYPE;
    }
    
    public function getPluginName() {
        $dirPlugin = realpath($this->getClassDirName().'/../..');
        if($dirPlugin){
            $dir = substr($dirPlugin , -11);
            if($dir && $dir==="ajaxcommand"){           
                $ret = "ajaxcommand";
            }else{
                $ret = parent::getPluginName();
            }
        }else{
            $ret = parent::getPluginName();
        }
        return $ret;
    }
    
    public function getPluginComponent() {
        $dirs = split("/", $this->getClassDirName());
        $length = sizeof($dirs);
        if($length>2){
            $dir = substr($dirs[$length-3], -11);
            if($dir && $dir==="ajaxcommand"){
                $ret = $dirs[$length-1];
            }else{
                $ret = parent::getPluginName();
            }
        }else{
            $ret = parent::getPluginName();
        }
        return $ret;
    }    
    
    public function getModelWrapper(){
        return $this->modelWrapper;
    }
    
    public function setModelWrapper($mw){
        $this->modelWrapper=$mw;
    }
    
    private function getClassDirName(){
        $thisClass = new ReflectionClass($this);   
        return dirname($thisClass->getFileName());
    }

    protected abstract function process();
}

?>
