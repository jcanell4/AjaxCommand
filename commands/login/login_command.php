<?php
/**
 * Description
 *
 * @author Josep Cañellas
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once (DOKU_COMMAND.'AjaxCmdResponseGenerator.php');
require_once (DOKU_COMMAND.'JsonGenerator.php');
require_once (DOKU_COMMAND.'abstract_command_class.php');


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
    
    protected function _run() {
        $response=array("loginRequest" => $this->params['do']==='login'
						,"loginResult" => false);

	if($this->params['do']==='login'){
            $response["loginResult"] = $this->isUserAuthenticated();
        }else if($this->isUserAuthenticated()){
            $this->_logoff();
            $response["loginResult"] = false;
        }
        return $response;
    }
    
    protected function getDefaultResponse($response, &$responseGenerator) {
        $responseGenerator->addLoginInfo(
                $response["loginRequest"], 
                $response["loginResult"]
        );
    }

    private function _logoff(){
        auth_logoff(true);
    }
}

?>
