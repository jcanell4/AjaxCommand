<?php
/**
 * Description of page_command
 *
 * @author Josep Cañellas
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once (DOKU_COMMAND.'JsonGenerator.php');
require_once (DOKU_COMMAND.'abstract_command_class.php');
require_once (DOKU_COMMAND.'ModelInterface.php');


class login_command extends abstract_command_class{

    public function __construct() {
        
        parent::__construct();
        $this->authenticatedUsersOnly=false;
        $this->types['do'] = abstract_command_class::T_STRING;
//        $this->types['id'] = abstract_command_class::T_STRING;
//        $this->types['u'] = abstract_command_class::T_STRING;
//        $this->types['p'] = abstract_command_class::T_STRING;

        $defaultValues=array('do' => 'login');
        $this->setParameters($defaultValues);    
    }
    
    public function getDokuwikiAct(){
        return $this->params['do'];
    }

    //tpl_content(((tpl_getConf("vector_toc_position") === "article") ? true : false));
    protected function _run() {
        global $conf;
        $ret=new ArrayJSonGenerator();
        $response=array("loginRequest" => $this->params['do']==='login'
						,"loginResult" => false);

		if($this->params['do']==='login'){
            $response["loginResult"] = $this->isUserAuthenticated();
        }
		else if($this->isUserAuthenticated()){
            $this->_logoff();
			$response["loginResult"] = false;
        }
		
        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::LOGIN_INFO,
				$response));	//afegir si és login(true) o logout(false)

        $ret->add(new BasicJsonGenerator(BasicJsonGenerator::SECTOK_DATA,
				getSecurityToken()));
        
        if($response["loginResult"]){
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::HTML_TYPE, 
					$this->modelInterface->getLoginPageResponse()));
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
					  array("type" => BasicJsonGenerator::CHANGE_WIDGET_PROPERTY,
							"id" => "exitButton", 
							"propertyName" => "visible", 
							"propertyValue" => true)));              
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
					  array("type" => BasicJsonGenerator::CHANGE_WIDGET_PROPERTY,
							"id" => "loginButton", 
							"propertyName" => "visible", 
							"propertyValue" => false)));      
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
                      array("type" => BasicJsonGenerator::RELOAD_WIDGET_CONTENT,
							"id" => "tb_index")));
			//elimina, si existe, la pestaña 'desconectat'
			$logout = $this->modelInterface->getLogoutPageResponse();
			$ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
					array("type" => BasicJsonGenerator::REMOVE_WIDGET_CHILD,
							"id" => $logout['id'])));
        }
		else{
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
					  array("type" => BasicJsonGenerator::CHANGE_WIDGET_PROPERTY,
							"id" => "exitButton", 
							"propertyName" => "visible", 
							"propertyValue" => false)));              
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
					  array("type" => BasicJsonGenerator::CHANGE_WIDGET_PROPERTY,
							"id" => "loginButton", 
							"propertyName" => "visible", 
							"propertyValue" => true)));              
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
                      array("type" => BasicJsonGenerator::RELOAD_WIDGET_CONTENT,
							"id" => "tb_index")));
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::COMMAND_TYPE, 
                      array("type" => BasicJsonGenerator::REMOVE_ALL_WIDGET_CHILDREN,
							"id" => "bodyContent")));
            //Josep: QUÊ VOLS FER AQUÍ? En parelem?
            //comento i torno a l'anterior
//			$arrLogout = $this->modelInterface->getLogoutPageResponse();
//			$arrLogout["isTab"] = false;
//            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::HTML_TYPE, 
//					$arrLogout));
            $ret->add(new BasicJsonGenerator(BasicJsonGenerator::HTML_TYPE, 
					$this->modelInterface->getLogoutPageResponse())); //TO DO internacionalització
        }
        return $ret->getJsonEncoded();
    }
    
    private function _logoff(){
//        require_once(DOKU_INC.'inc/auth.php');
        auth_logoff(true);
    }
}

?>
