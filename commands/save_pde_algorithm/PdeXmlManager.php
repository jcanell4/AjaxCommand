<?php

/**
 * Description 
 *
 * @author Daniel Criado Casas<dani.criado.casas@gmail.com>
 */
if (!defined('DOKU_INC'))
    die();
if (!defined('DOKU_PLUGIN'))
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND'))
    define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
if (!defined('DOKU_COMMAND_PDE'))
    define('DOKU_COMMAND_PDE', DOKU_COMMAND . "commands/save_pde_algorithm/");

class PdeXmlManager {
    private $command;
    private $params;
    private $xmlFileCreated;

    function PdeXmlManager($command) {
        $this->command = $command;
        $this->xmlFileCreated = @file_exists(DOKU_INC . $command->getConf("processingXmlFile"));
    }
    
    function setParams($params) {
        $this->params = $params;
    }

    /**
     * Informa sobre si un algorisme ja existeix en el fitxer XML.
     * @return int response code information
     */
    function existsAlgorithm($className) {
        $response = "";
        $xmlFile = $this->getXmlFile();
        $doc = new DOMDocument;
        $stringXml = file_get_contents($xmlFile);
        $doc->loadXML($stringXml);
        $valid = $doc->validate();
        if ($valid) {
            $node = $doc->getElementById($className);
        }
        if ($valid && $node) {
            $response = save_pde_algorithm_command::$ALGORITHM_EXISTS_CODE;
        } else {
            $response = save_pde_algorithm_command::$ALGORITHM_NOT_EXISTS_CODE;
        }
        return $response;
    }
    
    /**
     * Retorna el path del fitxer XML d'algorismes.
     * @return String Path to XML file.
     */
    private function getXmlFile() {
        if (!$this->xmlFileCreated) {
            $this->createXmlDataFile();
        }
        return DOKU_INC . $this->command->getConf("processingXmlFile");
    }
    
    private function createXmlDataFile() {
        file_put_contents($this->getXmlFile(), "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
                . "<!DOCTYPE algorismes [\n"
                . "<!ELEMENT algorismes (algorisme*)>\n"
                . "<!ELEMENT algorisme (nom , classe , descripcio)>\n"
                . "<!ATTLIST algorisme id ID #REQUIRED>\n"
                . "<!ELEMENT nom (#PCDATA)>\n"
                . "<!ELEMENT classe (#PCDATA)>\n"
                . "<!ELEMENT descripcio (#PCDATA)>\n"
                . "]>\n"
                . "<algorismes>\n"
                . "</algorismes>\n");
    }
    
        /**
     * Afegeix un algorisme al fitxer XML.
     * @param String $className identificador de l'algorisme.
     * @return bool True if pde algorithm was successfully added to xml file, False otherwise.
     */
    function addPdeAlgorithm($className) {
        $response = false;
        $id = $className;
        $nom = $this->params[save_pde_algorithm_command::$NOM_PARAM];
        $classe = str_replace(save_pde_algorithm_command::$SLASH, save_pde_algorithm_command::$DOT, $this->command->getConf('processingPackage')) . $className;
        if ($nom == null | $nom == "") {//Si el nom es buit, posar-li el nom de la classe
            $nom = $className;
        }
        if ($this->params[save_pde_algorithm_command::$DESCRIPCIO_PARAM]) {
            $descripcio = $this->params[save_pde_algorithm_command::$DESCRIPCIO_PARAM];
        } else {
            $descripcio = " \n";
        }
        $xmlFile = $this->getXmlFile();
        
        //$domDoc = new DOMDocument();
        $domDoc = DOMDocument::load($xmlFile);
        if($domDoc){
            $domDoc->preserveWhiteSpace = true;  
            $algorismesNod = $domDoc->documentElement;
            $algorismeNod = $domDoc->createElement(save_pde_algorithm_command::$ALGORISME_PARAM);
            $idAtt =$domDoc->createAttribute(save_pde_algorithm_command::$ID_PARAM);
            $idAtt->value=$id;
            $algorismeNod->appendChild($idAtt);
            $algorismeNod->appendChild($domDoc->createTextNode("\n"));
            
            $nomNod = $domDoc->createElement(save_pde_algorithm_command::$NOM_PARAM);
            $nomNod->appendChild($domDoc->createCDATASection($nom));
            $algorismeNod->appendChild($nomNod);
            $algorismeNod->appendChild($domDoc->createTextNode("\n"));
            
            $clNod = $domDoc->createElement(save_pde_algorithm_command::$CLASSE_PARAM);
            $clNod->appendChild($domDoc->createTextNode($classe));
            $algorismeNod->appendChild($clNod);
            $algorismeNod->appendChild($domDoc->createTextNode("\n"));

            $desNod = $domDoc->createElement(save_pde_algorithm_command::$DESCRIPCIO_PARAM);
            $desNod ->appendChild($domDoc->createCDATASection($descripcio));
            $algorismeNod->appendChild($desNod);
            $algorismeNod->appendChild($domDoc->createTextNode("\n"));
            
            $algorismesNod->appendChild($algorismeNod);
            $algorismesNod->appendChild($domDoc->createTextNode("\n"));
            
            $response = $domDoc->save($xmlFile);
        }
        return $response;
    }
    
    /**
     * Eliminar un algorisme del fitxer XML
     * @param String $className identificador de l'algorisme.
     * @return bool True if pde algorithm was successfully removed from xml file, False othewise.
     */
    private function removePdeAlgorithm($className) {
        $deleted = false;
        $xmlFile = $this->getXmlFile();
        $doc = new DOMDocument;
        $stringXml = file_get_contents($xmlFile);
        $doc->loadXML($stringXml);
        $valid = $doc->validate();
        if ($valid) {
            $node = $doc->getElementById($className);
        }
        if ($valid && $node) {
            $pnode = $node->parentNode;
            $node = $pnode->removeChild($node);
            $deleted = $node != null;
            if ($deleted) {
                $doc->formatOutput = TRUE;
                $saved = $doc->save($xmlFile);
            }
        } else {
            $deleted = FALSE;
        }
        return $deleted && $saved;
    }
    
    /**
     * Modifica un algorisme existent en el fitxer XML.
     * @param type $className identificador de l'algorisme
     * @return int response code information 
     */
    function modifyPdeAlgorithm($className) {
        $response = save_pde_algorithm_command::$XML_ERROR_CODE;
        if ($this->removePdeAlgorithm($className)) {
            if ($this->addPdeAlgorithm($className)) {
                $response = save_pde_algorithm_command::$OK_CODE;
            } else {
                $response = save_pde_algorithm_command::$XML_ERROR_CODE;
            }
        } else {
            $response = save_pde_algorithm_command::$XML_ERROR_CODE;
        }
        return $response;
    }

}
