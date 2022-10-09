<?php
/**
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if (!defined('DOKU_INC')) die();

class protected_processor_command extends abstract_command_class {
    private $protectedCommands;
    public function __construct() {
        parent::__construct();
        $this->authenticatedUsersOnly = TRUE;

        $this->types[PageKeys::KEY_DO] = self::T_STRING;
        $this->types[PageKeys::KEY_ID] = self::T_STRING;
        $this->types[RequestParameterKeys::KEY_COMMAND] = self::T_FUNCTION;
        $this->types[RequestParameterKeys::KEY_PARAMETERS] = self::T_JSON;
        
        $this->protectedCommands =["ProjectModelMethodCaller" =>["createTableRAPonderation"]];
    }

    protected function getDefaultResponse($response, &$responseGenerator) {
        if($response[ResponseHandlerKeys::TYPE]=="array"){
            $responseGenerator->addArrayTypeResponse($response[ResponseHandlerKeys::VALUE]);
        }
    }

    protected function process() {
        if(!isset($this->protectedCommands[$this->params[RequestParameterKeys::KEY_DO]])
                || ($this->protectedCommands[$this->params[RequestParameterKeys::KEY_DO]] !== "*"
                && !in_array($this->params[RequestParameterKeys::KEY_COMMAND], $this->protectedCommands[$this->params[RequestParameterKeys::KEY_DO]]))){
            throw new Exception("Unsupported action or command");
        }
        $actionName = $this->params[RequestParameterKeys::KEY_DO];
        $action = $this->getModelManager()->getActionInstance("{$actionName}Action");
        $response = $action->get($this->params);
        return $response;
    }
    
    public function getAuthorizationType() {
        return "basicCommand";
    }
}
