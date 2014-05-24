<?php

/**
 * Description 
 *
 * @author Daniel Criado Casas
 */
if (!defined('DOKU_INC'))
    die();
if (!defined('DOKU_PLUGIN'))
    define('DOKU_PLUGIN', DOKU_INC.'lib/plugins/');
if (!defined('DOKU_COMMAND'))
    define('DOKU_COMMAND', DOKU_PLUGIN."ajaxcommand/");
require_once (DOKU_COMMAND.'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND.'abstract_command_class.php');

//if (!defined('PROCESSING_XML_FILE'))
//    define('PROCESSING_XML_FILE',DOKU_INC.'lib/_java/pde/xml/algorismes.xml');

class get_pde_classes_info_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        if(@file_exists(DOKU_INC.'debug')){
            $this->authenticatedUsersOnly = false;
        }else{
            $this->authenticatedUsersOnly = true;
        }
    }

    protected function process() {
        $response = array();
        $response["code"] = -1;
        if (file_exists($this->getXmlFile())) {
            $sxml = simplexml_load_file($this->getXmlFile());
            if ($sxml) {
                $response["algorismes"] = $sxml;
                $response["code"] = 0;
            }
        } 
        return $response;
    }

    protected function preprocess() {
        
    }

    protected function startCommand() {
        
    }

    protected function getDefaultResponse($response, &$ret) {
        $response["info"] = "prueba";
//        switch ($response["code"]) {
//            case -1:
//                $response["info"] = $this->getLang('xml_unloaded');
//                break;
//            case 0:
//                $response["info"] = $this->getLang('xml_loaded');
//                break;
//            default:
//                $info = $this->getLang('unexpected_error');
//                break;
//        }
        $ret->addObjectTypeResponse($response);
    }
    
    private function getXmlFile(){
        return DOKU_INC.$this->getConf("processingXmlFile");
    }
}

?>
