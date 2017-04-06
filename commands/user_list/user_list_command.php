<?php
if (!defined('DOKU_INC')) {
    die();
}
if (!defined('DOKU_PLUGIN')) {
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
}
if (!defined('DOKU_COMMAND')) {
    define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
}
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_COMMAND . 'requestparams/PageKeys.php');

/**
 * Class edit_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class user_list_command extends abstract_command_class {
    public function __construct() {
        parent::__construct();
        $this->authenticatedUsersOnly = TRUE;
        
        $this->types[PageKeys::KEY_ID] = abstract_command_class::T_STRING;
        $this->types[PageKeys::KEY_DO] = abstract_command_class::T_STRING;
        $this->types[PageKeys::KEY_FILTER] = abstract_command_class::T_STRING;
        $this->types[PageKeys::KEY_START_POS] = abstract_command_class::T_INTEGER;
        $this->types[PageKeys::KEY_PROJECT] = abstract_command_class::T_INTEGER;

//        $defaultValues = ['do' => 'edit'];
//        $this->setParameters($defaultValues);
    }
    
    protected function getDefaultResponse($response, &$responseGenerator) {
        
    }

    protected function process() {
        if(!isset($this->types[PageKeys::KEY_DO]) && 
                isset($this->params[PageKeys::KEY_PROJECT]) && 
                isset($this->params[PageKeys::KEY_ID])){
            $this->types[PageKeys::KEY_DO] = "ofAProject";
        }elseif(!isset($this->types[PageKeys::KEY_DO]) && isset($this->params[PageKeys::KEY_ID])){
            $this->types[PageKeys::KEY_DO] = "byPagePermision";
        }elseif(!isset($this->types[PageKeys::KEY_DO])){
            $this->types[PageKeys::KEY_DO] = "byName";            
        }
        
    }
}
