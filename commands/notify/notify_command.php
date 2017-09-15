<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

/**
 * Class notify_command
 *
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
class notify_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types['do'] = abstract_command_class::T_STRING;
        $this->types['message'] = abstract_command_class::T_STRING;
        $this->types['to'] = abstract_command_class::T_STRING;
        $this->types['params'] = abstract_command_class::T_STRING;
        $this->types['changes'] = abstract_command_class::T_STRING;
        $this->types['since'] = abstract_command_class::T_INTEGER;
        
        $this->setParameterDefaultValues(array("since" => 0));
    }


    protected function process() {
        if (isset($this->params['params'])) {
            $this->params['params'] = json_decode($this->params['params'], true);
        }

        if (isset($this->params['changes'])) {
            $this->params['changes'] = json_decode($this->params['changes'], true);
        }


        return $this->modelWrapper->notify($this->params);
    }

    protected function getDefaultResponse($response, &$ajaxCmdResponseGenerator) {

        if (isset($response['info'])) {
            $ajaxCmdResponseGenerator->addInfoDta($response['info']);
        }

        foreach ($response['notifications'] as $notification) {
            $ajaxCmdResponseGenerator->addNotificationResponse($notification['action'], $notification['params']);
        }

    }

    public function getAuthorizationType() {
        return "_none";
    }
}
