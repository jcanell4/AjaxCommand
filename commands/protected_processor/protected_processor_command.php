<?php
/**
 * Class user_list_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if (!defined('DOKU_INC')) die();

class protected_processor_command extends abstract_command_class {
    public function __construct() {
        parent::__construct();
        $this->authenticatedUsersOnly = TRUE;

        $this->types[PageKeys::KEY_DO] = self::T_STRING;
        $this->types[PageKeys::KEY_ID] = self::T_STRING;
        $this->types[RequestParameterKeys::KEY_COMMAND] = self::T_FUNCTION;
        $this->types[RequestParameterKeys::KEY_PARAMETERS] = self::T_JSON;
    }

    protected function getDefaultResponse($response, &$responseGenerator) {
        if($response[ResponseHandlerKeys::TYPE]=="array"){
            $responseGenerator->addArrayTypeResponse($response[ResponseHandlerKeys::VALUE]);
        }
    }

    protected function process() {
        $actionName = $this->params[RequestParameterKeys::KEY_DO];
        $action = $this->getModelManager()->getActionInstance("{$actionName}Action");
        $response = $action->get($this->params);
        return $response;
    }
    
    public function getAuthorizationType() {
        return "basicCommand";
    }
}
