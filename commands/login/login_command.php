<?php
/**
 * Description of page_command
 *
 * @author Josep Cañellas
 */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once(DOKU_COMMAND.'JsonGenerator.php');
require_once(DOKU_COMMAND.'abstract_command_class.php');
require_once (DOKU_COMMAND.'ModelInterface.php');


class login_command extends abstract_command_class{

    public function __construct() {
        
        parent::__construct();
        $this->authenticatedUsersOnly=false;
        $this->types['do'] = abstract_command_class::T_STRING;
//        $this->types['id'] = abstract_command_class::T_STRING;
//        $this->types['u'] = abstract_command_class::T_STRING;
//        $this->types['p'] = abstract_command_class::T_STRING;

        $defaultValues = array(
            'do' => 'login',
        );

        $this->setParameters($defaultValues);        
    }
    
    //tpl_content(((tpl_getConf("vector_toc_position") === "article") ? true : false));
    protected function _run() {
        global $conf;
        $ret=new ArrayJSonGenerator();
        $response=false;
        if($this->params['do']==='login'){
            $response = $this->isUserAuthenticated();
        }else if($this->isUserAuthenticated()){
            $this->_logoff();
        }
        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::LOGIN_INFO, 
                $response));
        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::SECTOK_DATA, 
                getSecurityToken()));
        
        if($response){
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::DATA_TYPE, 
            ModelInterface::getLoginPageResponse()));
//            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
//                array("type" => BasicJsonGenerator::CHANGE_DOM_STYLE,
//                      "id" => "loginButton", 
//                      "propertyName" => "display", 
//                      "propertyValue" => "none")));  
//            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
//                array("type" => BasicJsonGenerator::CHANGE_DOM_STYLE,
//                      "id" => "exitButton", 
//                      "propertyName" => "display", 
//                      "propertyValue" => "")));  
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
                array("type" => BasicJsonGenerator::CHANGE_WIDGET_PROPERTY,
                      "id" => "exitButton", 
                      "propertyName" => "visible", 
                      "propertyValue" => true)));              
        }else{
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::DATA_TYPE, 
            ModelInterface::getLogoutPageResponse())); //TO DO internacionalització
//            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
//                array("type" => BasicJsonGenerator::CHANGE_DOM_STYLE,
//                      "id" => "loginButton", 
//                      "propertyName" => "display", 
//                      "propertyValue" => "")));  
//            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
//                array("type" => BasicJsonGenerator::CHANGE_DOM_STYLE,
//                      "id" => "exitButton", 
//                      "propertyName" => "display", 
//                      "propertyValue" => "none")));  
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
                array("type" => BasicJsonGenerator::CHANGE_WIDGET_PROPERTY,
                      "id" => "exitButton", 
                      "propertyName" => "visible", 
                      "propertyValue" => false)));              
        }

        
        return $ret->getJsonEncoded();
    }
    
    private function _logoff(){
//        require_once(DOKU_INC.'inc/auth.php');
        auth_logoff(true);
    }
}

?>
