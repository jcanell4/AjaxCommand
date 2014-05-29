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
require_once (DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

class save_pde_algorithm_command extends abstract_command_class {
    /*     * Codi d'informació per quan un fitxer no s'ha pogut dessar correctament.
     * @return integer Retorna un -1
     */

//    private static $SAVE_FILE_INCORRECT_CODE = -1;
//    
//    /**Codi d'informació per quan un fitxer s'ha dessat correctament.
//     * @return integer Retorna un 1
//     */
//    private static $SAVE_FILE_CORRECT_CODE = 1;
//    
//    /**Codi d'informació per quan un fitxer ja existeix.
//     * @return integer Retorna un -2
//     */
//    private static $FILENAME_EXISTS_CODE = -2;
//    
//    /**Codi d'informació per quan un fitxer no existeix.
//     * @return integer Retorna un 2
//     */
//    private static $FILENAME_NOT_EXISTS_CODE = 2;
//    
//    /**Codi d'informació per quan una comanda no estava definida.
//     * @return integer Retorna un -10
//     */
//    private static $UNDEFINED_COMMAND_CODE = -10;
//    
//    //Comandas
//    private static $EXISTS_IMAGE_NAME_PARAM = 'existsImageName';
//    private static $SAVE_IMAGE_PARAM = 'saveImage';
//    private static $IMAGE_NAME_PARAM = 'imageName';
    private static $ID_PARAM = 'id';
    //Parametres del fitxer
    private static $FILE_PARAM = 'uploadedfile';
//    private static $PDE_MIME_TYPE = 'text/plain';
    private static $PDE_MIME_TYPE = 'application/octet-stream';
    private static $PDE_EXTENSION = '.pde';
    private static $JAVA_EXTENSION = '.java';

    public function __construct() {
        parent::__construct();
        if (@file_exists(DOKU_INC . 'debug')) {
            $this->authenticatedUsersOnly = false;
        } else {
            $this->authenticatedUsersOnly = true;
        }
    }

    protected function process() {
        //Validar fitxer, https://dojotoolkit.org/reference-guide/1.9/dojox/form/Uploader.html#missing-features

        if (array_key_exists(self::$FILE_PARAM, $this->params)) {
            $file = $this->params[self::$FILE_PARAM];
            $filePath = $file[self::$FILE_CONTENT_PARAM];
            $fileName = $file[self::$FILENAME_PARAM];
            $repositoryPath = $this->getPdeRepositoryDir();
            $pdePath = $repositoryPath . $fileName;
            if ($this->isPdeFile($file)) {
                if (!file_exists($pdePath)) {
                    if ($this->movePdeToRepository($filePath, $pdePath)) {
                        $className = ucfirst($this->params[self::$ID_PARAM]);
                        if ($this->generateJavaClass($className, $pdePath)) {
                            $this->addPdeAlgorithm($className);
                        } else {
                            $this->removePdeFromRepository($pdePath);
                        }
                    }
                }
            }
        }

        return $response;
    }

    protected function getDefaultResponse($response, &$ret) {
        $responseCode = $response;
        $info = "";
        $ret->addCodeTypeResponse($responseCode, $info);
    }

    /**
     * Funcio trobada a:
     * http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
     * @param type $haystack
     * @param type $needle
     * @return boolean True if ends with $needle, False otherwise
     */
    private function endsWith($haystack, $needle) {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * Ens diu si el fitxer carregat per post es un fitxer .pde o no.
     * @return boolean True si el fitxer te extensió .pde, False altrament.
     */
    private function isPdeFile($file) {
        $pdeFile = false;
        if ($file[self::$ERROR_PARAM] == UPLOAD_ERR_OK && $file[self::$FILE_TYPE_PARAM] == self::$PDE_MIME_TYPE && $this->endsWith($file[self::$FILENAME_PARAM], self::$PDE_EXTENSION) && is_uploaded_file($file[self::$FILE_CONTENT_PARAM])) {
            $pdeFile = true;
        }
        return $pdeFile;
    }

    /**
     * Mou el fitxer temporal carregat al servidor al repositori de fitxers PDE.
     * @return True si s'ha pogut moure, False altrament.
     * Potser hauria de retornar 0 si ha anat be, i un negatiu per indicar qualsevol altre cosa.
     */
    private function movePdeToRepository($filePath, $pdePath) {
        return move_uploaded_file($filePath, $pdePath);
    }

    /* Elimina un fitxer pde del repository
     * 
     * @return type
     */

    private function removePdeFromRepository($pdePath) {
        return unlink($pdePath);
    }

    /**
     * Genera i compila l'algorisme Pde.
     */
    private function generateJavaClass($className, $pdePath) {
        $javaPath = $this->getSrcRepositoryDir() . $this->getConf('processingPackage') . $className . self::$JAVA_EXTENSION;
        $generated = $this->generateSource($className, $javaPath, $pdePath);
        if ($generated) {
            $generated = $this->compileSource($javaPath);
        }
        return $generated;
    }

    /**
     * Genera el fitxer .java amb el fitxer .pde
     * @param type $pdePath
     */
    private function generateSource($className, $javaPath, $pdePath) {
        $data = "package ioc.wiki.processingmanager;\n";
        $data .= "public class " . $className . " extends ImageGenerator {\n";
        $contentPde = file_get_contents($pdePath);
        $data .= $contentPde;
        $data .= "}";
        return file_put_contents($javaPath, $data) > 0;
    }

    /**
     * Genera el fitxer .class compilant el fitxer .java
     * @param type $javaPath
     */
    private function compileSource($javaPath) {
        $pathClasses = $this->getClassesRepositoryDir();
        $pathLibs = $this->getJavaLibDir();
        //Comanda que funciona
        //javac -classpath ../../../../classes/:../../../../../lib/core.jar IdDani.java
        $command = "javac -d " . $pathClasses ." -classpath " . $pathClasses . ":" . $pathLibs . "core.jar " . $javaPath;
        exec($command, $output, $returnVar);
        return $returnVar == 0;

    }

    private function addPdeAlgorithm($className) {
        $id = $this->params["id"];
        $nom = $this->params["nom"];
        $classe = str_replace('/','.',$this->getConf('processingPackage')).$className;
        $descripcio = $this->params["descripcio"];
        
        $xmlFile = $this->getXmlFile();
        $xml = simplexml_load_file($xmlFile);
        $algorisme = $xml->addChild('algorisme');
        $algorisme->addChild('id', $id);
        $algorisme->addChild('nom', $nom);
        $algorisme->addChild('classe', $classe);
        $algorisme->addChild('descripcio', $descripcio);
        $xml->asXML($xmlFile);
    }

    private function getXmlFile() {
        return DOKU_INC . $this->getConf("processingXmlFile");
    }

    /**
     * Consulta el directori definit per les imatges de processing.
     * @global type $conf
     * @return type Retorna el directori definit per les imatges de processing.
     */
    private function getPdeRepositoryDir() {
        global $conf;
        return DOKU_INC . $this->getConf('processingPdeRepository');
    }

    /**
     * Consulta el directori definit per les imatges de processing.
     * @global type $conf
     * @return type Retorna el directori definit per les imatges de processing.
     */
    private function getClassesRepositoryDir() {
        global $conf;
        return DOKU_INC . $this->getConf('processingClassesRepository');
    }

    /**
     * Consulta el directori definit per les imatges de processing.
     * @global type $conf
     * @return type Retorna el directori definit per les imatges de processing.
     */
    private function getSrcRepositoryDir() {
        global $conf;
        return DOKU_INC . $this->getConf('processingSrcRepository');
    }

    private function getJavaLibDir() {
        global $conf;
        return DOKU_INC . $this->getConf('javaLib');
    }

}

?>