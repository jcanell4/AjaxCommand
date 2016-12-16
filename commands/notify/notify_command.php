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
    }


    protected function process() {
        if (isset($this->params['params'])) {
            $this->params['params'] = json_decode($this->params['params'], true);
        }

        return $this->modelWrapper->notify($this->params);
    }

    protected function getDefaultResponse($response, &$ret) {

        if ($response['notifications']) {
            for ($i=0; $i<count($response['notifications']);$i++) {
                $action = $response['notifications'][$i]['action'];
                $params = $response['notifications'][$i]['params'];

                $ret->addNotification($action, $params);
            }


        } else {
            $action = $response['action'];
            $params = $response['params'];

            $ret->addNotification($action, $params);
        }

    }
    
    public function getAuthorizationType() {
        return "_none";
    }
}
