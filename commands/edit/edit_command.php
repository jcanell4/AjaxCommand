<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

/**
 * Class edit_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class edit_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
//        $this->types['idx'] = abstract_command_class::T_STRING;
        $this->types['do']    = abstract_command_class::T_STRING;
        $this->types['rev']   = abstract_command_class::T_STRING;
        $this->types['range'] = abstract_command_class::T_STRING;
        $this->types['date']  = abstract_command_class::T_STRING;

        $defaultValues = array(
            'id' => 'index',
            'do' => 'edit',
        );

        $this->setParameters($defaultValues);
    }

    protected function process() {
        $contentData = $this->modelWrapper->getCodePage(
                                          $this->params['do'],
                                          $this->params['id'],
                                          $this->params['rev'],
                                          $this->params['range']
        );
        return $contentData;
    }

    protected function getDefaultResponse($response, &$ret) {
        $ret->addWikiCodeDoc(
            $response["id"], $response["ns"],
            $response["title"], $response["content"]
        );
    }
}