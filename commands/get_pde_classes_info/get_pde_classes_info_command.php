<?php
if (!defined('DOKU_INC')) die();

/**
 * Class get_pde_classes_info_command
 * @author Daniel Criado Casas<dani.criado.casas@gmail.com>
 */
class get_pde_classes_info_command extends abstract_command_class {

    const UNLOADED_XML_CODE = -1; //indica que un fitxer xml no s'ha pogut carregar correctament
    const LOADED_XML_CODE = 0;    //indica que un fitxer xml s'ha pogut carregar correctament

    public function __construct() {
        parent::__construct();
        if (@file_exists(DOKU_INC . 'debug')) {
            $this->authenticatedUsersOnly = FALSE;
        } else {
            $this->authenticatedUsersOnly = TRUE;
        }
    }

    protected function process() {
        $response                 = array();
        $response["code"]         = self::UNLOADED_XML_CODE;
        $response["n_algorismes"] = self::UNLOADED_XML_CODE;
        if (file_exists($this->getXmlFile())) {
            $sxml = simplexml_load_file($this->getXmlFile(), "SimpleXMLElement", LIBXML_NOCDATA);
            if ($sxml) {
                $response["n_algorismes"] = $sxml->count();
                $response["algorismes"]   = $sxml;
                $response["code"]         = self::LOADED_XML_CODE;
            }
        }
        return $response;
    }

    protected function getDefaultResponse($response, &$ret) {
        $response["info"] = "";
        switch ($response["code"]) {
            case self::UNLOADED_XML_CODE:
                $response["info"] = $this->getLang('unloadedXml');
                break;
            case self::LOADED_XML_CODE:
                $response["info"] = $this->getLang('loadedXml');
                break;
            default:
                $response["info"] = $this->getLang('unexpectedError');
                break;
        }
        $ret->addObjectTypeResponse($response);
    }

    /**
     * Retorna el path al fitxer XML d'algorismes
     * @return string path al fitxer XML d'algorismes
     */
    private function getXmlFile() {
        return DOKU_INC . $this->getConf("processingXmlFile");
    }
}