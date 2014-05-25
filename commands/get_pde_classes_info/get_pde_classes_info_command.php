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

class get_pde_classes_info_command extends abstract_command_class {

    
    /**Codi d'informació per quan un fitxer xml no s'ha pogut carregar correctament.
     * @return integer Retorna un -1
     */
    private static $UNLOADED_XML_CODE = -1;
    
    /**Codi d'informació per quan un fitxer xml s'ha pogut carregar correctament.
     * @return integer Retorna un 0
     */
    private static $LOADED_XML_CODE = 0;
    
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
        $response["code"] = self::$UNLOADED_XML_CODE;
        if (file_exists($this->getXmlFile())) {
            $sxml = simplexml_load_file($this->getXmlFile());
            if ($sxml) {
                $response["algorismes"] = $sxml;
                $response["code"] = self::$LOADED_XML_CODE;
            }
        } 
        return $response;
    }

    protected function getDefaultResponse($response, &$ret) {
        $response["info"] = "";
        switch ($response["code"]) {
            case self::$UNLOADED_XML_CODE:
                $response["info"] = $this->getLang('unloadedXml');
                break;
            case self::$LOADED_XML_CODE:
                $response["info"] = $this->getLang('loadedXml');
                break;
            default:
                $response["info"] = $this->getLang('unexpectedError');
                break;
        }
        $ret->addObjectTypeResponse($response);
    }
    
    private function getXmlFile(){
        return DOKU_INC.$this->getConf("processingXmlFile");
    }
}

?>
