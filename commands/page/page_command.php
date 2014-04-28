<?php
/**
 * Description of page_command
 *
 * @author Josep Cañellas
 */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once (DOKU_COMMAND.'AjaxCmdResponseGenerator.php');
require_once (DOKU_COMMAND.'JsonGenerator.php');
require_once(DOKU_COMMAND.'abstract_command_class.php');
//require_once (DOKU_COMMAND.'DokuModelWrapper.php');

class page_command extends abstract_command_class{

    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
//        $this->types['idx'] = abstract_command_class::T_STRING;
        $this->types['rev'] = abstract_command_class::T_STRING;

        $defaultValues = array(
            'id' => 'start',
//            'do' => 'show',
        );

        $this->setParameters($defaultValues);        
    }

    protected function process() {
        $contentData = $this->modelWrapper->getHtmlPage(
                $this->params['id'],
                $this->params['rev']
        );
        return $contentData;
    }
    
    protected function getDefaultResponse($response, &$responseGenerator) {
        $responseGenerator->addHtmlDoc($contentData["id"], $contentData["ns"],
                    $contentData["title"], $contentData["content"]);
    }
}

?>
