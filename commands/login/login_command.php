<?php
/**
 * Description
 *
 * @author Josep Cañellas
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once (DOKU_COMMAND.'AjaxCmdResponseHandler.php');
require_once (DOKU_COMMAND.'JsonGenerator.php');
require_once (DOKU_COMMAND.'abstract_page_process_cmd.php');
require_once (DOKU_COMMAND.'DokuModelWrapper.php');


class login_command extends abstract_page_process_cmd{

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
    
    public function getDwAct(){
        return $this->params['do'];
    }
    
    protected function preprocess() {
        $this->modelWrapper->doFormatedPagePreProcess();
    }

    //tpl_content(((tpl_getConf("vector_toc_position") === "article") ? true : false));
    protected function _run() {
        return $this->getResponse();
    }
    
    private function getResponse(){
        global $conf;
        $ret = new AjaxCmdResponseHandler();
        //$ret=new ArrayJSonGenerator();
        $response=array("loginRequest" => $this->params['do']==='login'
						,"loginResult" => false);

		if($this->params['do']==='login'){
            $response["loginResult"] = $this->isUserAuthenticated();
        }
		else if($this->isUserAuthenticated()){
            $this->_logoff();
			$response["loginResult"] = false;
        }
		
        $ret->addLoginInfo($response);
        $ret->addSectokData(getSecurityToken());
//        $ret->add(new ResponseGenerator(ResponseGenerator::LOGIN_INFO,
//				$response));	
//        $ret->add(new ResponseGenerator(ResponseGenerator::SECTOK_DATA,
//				getSecurityToken()));        
        if($response["loginResult"]){
            $ret->addSetJsInfo($this->modelWrapper->getJsInfo());
            $ret->addHtmlDoc($this->modelWrapper->getLoginPageResponse());
            $ret->addChangeWidgetProperty("exitButton", "visible", true);
            $ret->addChangeWidgetProperty("loginButton", "visible", false);
            $ret->addReloadWidgetContent("tb_index");
            //elimina, si existe, la pestaña 'desconectat'
            $logout = $this->modelWrapper->getLogoutPageResponse();
            $ret->addRemoveWidgetChild($logout['id']);
            $sig = $this->modelWrapper->getSignature();
            
//            $ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
//                  array("type" => ResponseGenerator::JSINFO,
//		    "value" => $this->modelWrapper->getJsInfo(),
//                )));                              
//            $ret->add(new ResponseGenerator(ResponseGenerator::HTML_TYPE, 
//					$this->modelWrapper->getLoginPageResponse()));
//            $ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
//					  array("type" => ResponseGenerator::CHANGE_WIDGET_PROPERTY,
//							"id" => "exitButton", 
//							"propertyName" => "visible", 
//							"propertyValue" => true)));              
//            $ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
//					  array("type" => ResponseGenerator::CHANGE_WIDGET_PROPERTY,
//							"id" => "loginButton", 
//							"propertyName" => "visible", 
//							"propertyValue" => false)));      
//            $ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
//                      array("type" => ResponseGenerator::RELOAD_WIDGET_CONTENT,
//							"id" => "tb_index")));
			//elimina, si existe, la pestaña 'desconectat'
//			$logout = $this->modelWrapper->getLogoutPageResponse();
//			$ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
//					array("type" => ResponseGenerator::REMOVE_WIDGET_CHILD,
//							"id" => $logout['id'])));
        }else{
            $ret->addChangeWidgetProperty("exitButton", "visible", false);
            $ret->addChangeWidgetProperty("loginButton", "visible", true);
            $ret->addReloadWidgetContent("tb_index");
            $ret->addRemoveAllWidgetChildren("bodyContent");
            $ret->addRemoveAllWidgetChildren("zonaMetaInfo");
            $ret->addHtmlDoc($this->modelWrapper->getLogoutPageResponse());
            $sig = '';
//            $ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
//					  array("type" => ResponseGenerator::CHANGE_WIDGET_PROPERTY,
//							"id" => "exitButton", 
//							"propertyName" => "visible", 
//							"propertyValue" => false)));              
//            $ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
//					  array("type" => ResponseGenerator::CHANGE_WIDGET_PROPERTY,
//							"id" => "loginButton", 
//							"propertyName" => "visible", 
//							"propertyValue" => true)));              
//            $ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
//                      array("type" => ResponseGenerator::RELOAD_WIDGET_CONTENT,
//							"id" => "tb_index")));
//            $ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
//                      array("type" => ResponseGenerator::REMOVE_ALL_WIDGET_CHILDREN,
//							"id" => "bodyContent")));
//            $ret->add(new ResponseGenerator(ResponseGenerator::COMMAND_TYPE, 
//                      array("type" => ResponseGenerator::REMOVE_ALL_WIDGET_CHILDREN,
//							"id" => "zonaMetaInfo")));
//            $ret->add(new ResponseGenerator(ResponseGenerator::HTML_TYPE, 
//					$this->modelWrapper->getLogoutPageResponse())); 
        }
        $ret->addProcessFunction(true, "ioc/dokuwiki/setSignature", $sig);
//        return $ret->getJsonEncoded();  
        return $ret->getResponse();
    }
    
    private function _logoff(){
//        require_once(DOKU_INC.'inc/auth.php');
        auth_logoff(true);
    }
}

?>
