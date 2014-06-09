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
    /* Codi d'informació per quan ha anat tot correctament.
     * @return integer Retorna un 0
     */

    private static $OK_CODE = 0;


    /* Codi d'informació per quan un algorisme ja existeix.
     * @return integer Retorna un -2
     */
    private static $ALGORITHM_EXISTS_CODE = -2;

    /* Codi d'informació per quan un algorisme no existeix.
     * @return integer Retorna un 2
     */
    private static $ALGORITHM_NOT_EXISTS_CODE = 2;

    /* Codi d'informació per quan hi ha hagut algun error amb el fitxer XML.
     * @return integer Retorna un -6
     */
    private static $XML_ERROR_CODE = -6;

    /* Codi d'informació per quan un fitxer d'algorisme no s'ha pogut compilar.
     * @return integer Retorna un -7
     */
    private static $UNCOMPILED_ALGORITHM_CODE = -7;

    /* Codi d'informació per quan un fitxer d'algorisme no ha estat carregat.
     * @return integer Retorna un -8
     */
    private static $UNLOADED_ALGORITHM_CODE = -8;

    /* Codi d'informació per quan una comanda no estava definida.
     * @return integer Retorna un -9
     */
    private static $UNDEFINED_ALGORITHM_NAME_CODE = -9;

    /* Codi d'informació per quan una comanda no estava definida.
     * @return integer Retorna un -10
     */
    private static $UNDEFINED_COMMAND_CODE = -10;
//    
    //Comandas
    private static $COMMAND_PARAM = "do";
    private static $EXISTS_ALGORITHM_PARAM = 'existsAlgorithm';
    private static $MODIFY_ALGORITHM_PARAM = 'modifyAlgorithm';
    private static $APPEND_ALGORITHM_PARAM = 'appendAlgorithm';
    private static $ALGORITHM_NAME_PARAM = 'algorithmName';
    //Params de l'algorisme
    private static $ALGORISME_PARAM = "algorisme";
    private static $ID_PARAM = "id";
    private static $NOM_PARAM = "nom";
    private static $DESCRIPCIO_PARAM = "descripcio";
    private static $CLASSE_PARAM = "classe";
    //Parametres del fitxer
    private static $FILE_PARAM = 'uploadedfile';
    private static $PDE_MIME_TYPE = 'application/octet-stream';
    private static $PDE_EXTENSION = '.pde';
    private static $JAVA_EXTENSION = '.java';
    private static $COMMA = ",";
    private static $DOT = ".";
    private static $SLASH = "/";
    private static $TWO_DOTS = ":";

    public function __construct() {
        parent::__construct();
        if (@file_exists(DOKU_INC . 'debug')) {
            $this->authenticatedUsersOnly = false;
        } else {
            $this->authenticatedUsersOnly = true;
        }
    }

    protected function process() {
        $response = self::$UNDEFINED_COMMAND_CODE;
        if (array_key_exists(self::$COMMAND_PARAM, $this->params)) {
            $do = $this->params[self::$COMMAND_PARAM];
            switch ($do) {
                case self::$EXISTS_ALGORITHM_PARAM:
                    $response = $this->existsAlgorithm();
                    break;
                case self::$MODIFY_ALGORITHM_PARAM:
                    $response = $this->modifyAlgorithm();
                    break;
                case self::$APPEND_ALGORITHM_PARAM:
                    $response = $this->appendAlgorithm();
                    break;
                default:
                    break;
            }
        }
        return $response;
    }

    protected function getDefaultResponse($response, &$ret) {
        $responseCode = $response;
        $info = "";
        switch ($responseCode) {
            case self::$OK_CODE:
                $info = $this->getLang('ok');
                break;
            case self::$ALGORITHM_EXISTS_CODE:
                $info = $this->getLang('algorithmExists');
                break;
            case self::$ALGORITHM_NOT_EXISTS_CODE:
                $info = $this->getLang('algorithmNotExists');
                break;
            case self::$XML_ERROR_CODE:
                $info = $this->getLang('xmlError');
                break;
            case self::$UNCOMPILED_ALGORITHM_CODE:
                $info = $this->getLang('uncompiledAlgorithm');
                break;
            case self::$UNLOADED_ALGORITHM_CODE:
                $info = $this->getLang('unloadedAlgorithm');
                break;
            case self::$UNDEFINED_COMMAND_CODE:
                $info = $this->getLang('undefinedCommand');
                break;
            default:
                $info = $this->getLang('unexpectedError');
                break;
        }
        $ret->addCodeTypeResponse($responseCode, $info);
    }

    /**
     * Informa sobre si un algorisme ja existeix.
     * @return type
     */
    private function existsAlgorithm() {
        $response = self::$UNDEFINED_ALGORITHM_NAME_CODE;
        if (array_key_exists(self::$ALGORITHM_NAME_PARAM, $this->params)) {
            $algorithmName = $this->params[self::$ALGORITHM_NAME_PARAM]; //fitxer pde
            $repositoryPdePath = $this->getPdeRepositoryDir();
            $pdePath = $repositoryPdePath . $algorithmName;
            if (file_exists($pdePath)) {
                $response = self::$ALGORITHM_EXISTS_CODE;
            } else {
                $response = self::$ALGORITHM_NOT_EXISTS_CODE;
            }
        }
        return $response;
    }
    
    /**
     * Modifica un algorisme ja existent.
     * @return int response code information
     */
    private function modifyAlgorithm() {
        $response = self::$UNLOADED_ALGORITHM_CODE;
        if (array_key_exists(self::$FILE_PARAM, $this->params)) {
            $file = $this->params[self::$FILE_PARAM];
            $filePath = $file[self::$FILE_CONTENT_PARAM]; //path del fitxer temporal
            $fileName = $this->params[self::$ALGORITHM_NAME_PARAM]; //nomdelfitxer.pde
            $repositoryPdePath = $this->getPdeRepositoryDir();
            $pdePath = $repositoryPdePath . $fileName;
            if ($this->isPdeFile($file)) {
                if ($this->movePdeToRepository($filePath, $pdePath)) {
                    $className = ucfirst(substr($fileName, 0, -4)); //Li treu la extensio .pde i capitalitza el string
                    if ($this->generateJavaClass($className, $pdePath)) {
                        $reponse = $this->modifyPdeAlgorithm($className);
                    } else {
                        $this->removePdeFromRepository($pdePath);
                        $response = self::$UNCOMPILED_ALGORITHM_CODE;
                    }
                }
            }
        }
        return $response;
    }

    /**
     * Afegeix un algorisme al servidor amb el fitxer .pde carregat per l'usuari.
     * @return int response code information
     */
    private function appendAlgorithm() {
        $response = self::$UNLOADED_ALGORITHM_CODE;
        //Validar fitxer perque sigui .pde, https://dojotoolkit.org/reference-guide/1.9/dojox/form/Uploader.html#missing-features
        if (array_key_exists(self::$FILE_PARAM, $this->params)) {
            $file = $this->params[self::$FILE_PARAM];
            $filePath = $file[self::$FILE_CONTENT_PARAM]; //path del fitxer temporal
            $fileName = $this->params[self::$ALGORITHM_NAME_PARAM]; //nomdelfitxer.pde
            $repositoryPdePath = $this->getPdeRepositoryDir();
            $pdePath = $repositoryPdePath . $fileName;
            if ($this->isPdeFile($file)) {
                if (!file_exists($pdePath)) {
                    $this->modelWrapper->makeFileDir($pdePath); //assegura que el directori existeix
                    if ($this->movePdeToRepository($filePath, $pdePath)) {
                        $className = ucfirst(substr($fileName, 0, -4)); //Li treu la extensio .pde i capitalitza el string
                        if ($this->generateJavaClass($className, $pdePath)) {
                            if ($this->addPdeAlgorithm($className)) {
                                $response = self::$OK_CODE;
                            } else {
                                $response = self::$XML_ERROR_CODE;
                            }
                        } else {
                            $this->removePdeFromRepository($pdePath);
                            $response = self::$UNCOMPILED_ALGORITHM_CODE;
                        }
                    }
                } else {
                    $response = self::$ALGORITHM_EXISTS_CODE;
                }
            }
        }
        return $response;
    }

    /**
     * Funcio trobada a:
     * http://stackoverflow.com/questions/834303/startswith-and-endswith-functions-in-php
     * @param type $haystack
     * @param type $needle
     * @return bool True if ends with $needle, False otherwise
     */
    private function endsWith($haystack, $needle) {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * Ens diu si el fitxer carregat per post es un fitxer .pde o no.
     * @return bool True si el fitxer te extensió .pde, False altrament.
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
     * @return bool True on succes, False otherwise
     */
    private function movePdeToRepository($filePath, $pdePath) {
        return move_uploaded_file($filePath, $pdePath);
    }

    /** Elimina un fitxer pde del repository
     * @return bool True on succes, False otherwise
     */
    private function removePdeFromRepository($pdePath) {
        return unlink($pdePath);
    }

    /**
     * Genera i compila l'algorisme Pde.
     * @param type $className Nom de la classe Java
     * @param type $pdePath path del fitxer Pde
     * @return bool True on success, False otherwise 
     */
    private function generateJavaClass($className, $pdePath) {
        $javaPath = $this->getSrcRepositoryDir() . $this->getConf('processingPackage') . $className . self::$JAVA_EXTENSION;
        $this->modelWrapper->makeFileDir($javaPath);
        $generated = $this->generateSource($className, $javaPath, $pdePath);
        if ($generated) {
            $generated = $this->compileSource($javaPath);
        }
        if (!$generated) {
            unlink($javaPath); //Eliminar source en cas de fallada de compilació
        }
        return $generated;
    }

    /**
     * Genera el fitxer .java amb el fitxer .pde
     * @param type $className Nom de la classe Java
     * @param type $javaPath path del fitxer Java a generar
     * @param type $pdePath path del fitxer Pde
     * @return bool True on success, False otherwise
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
     * @param type $javaPath path del fitxer Java a compilar
     * @return bool True on succes, False otherwise
     */
    private function compileSource($javaPath) {
        $pathClasses = $this->getClassesRepositoryDir();
        $pathLibs = $this->getJavaLibDir();
        $libNames = $this->getConf('javaLibs');
        $arrayLibs = split(self::$COMMA, $libNames);
        $libs = "";
        foreach ($arrayLibs as $lib) {
            $libs .= $pathLibs . $lib . self::$TWO_DOTS;
        }
        $libs = substr($libs, 0, -1); //Treu els ultims dos punts que sobren.
        //Comanda que funciona
        //javac -classpath ../../../../classes/:../../../../../lib/core.jar IdDani.java
        $command = "javac -d " . $pathClasses . " -classpath " . $libs . " " . $javaPath;
        exec($command, $output, $returnVar);
        return $returnVar == 0;
    }

    /**
     * Afegeix un algorisme al fitxer XML.
     * @param String $className identificador de l'algorisme.
     * @return bool True if pde algorithm was successfully added to xml file, False otherwise.
     */
    private function addPdeAlgorithm($className) {
        $response = false;
        $id = $className;
        $nom = $this->params[self::$NOM_PARAM];
        $classe = str_replace(self::$SLASH, self::$DOT, $this->getConf('processingPackage')) . $className;
        if ($nom == null | $nom == "") {//Si el nom es buit, posar-li el nom de la classe
            $nom = $className;
        }
        $descripcio = $this->params[self::$DESCRIPCIO_PARAM];

        $xmlFile = $this->getXmlFile();
        $xml = simplexml_load_file($xmlFile);
        if ($xml) {
            $algorisme = $xml->addChild(self::$ALGORISME_PARAM);
            $algorisme->addChild(self::$ID_PARAM, $id);
            $algorisme->addChild(self::$NOM_PARAM, $nom);
            $algorisme->addChild(self::$CLASSE_PARAM, $classe);
            $algorisme->addChild(self::$DESCRIPCIO_PARAM, $descripcio);
            $response = $xml->asXML($xmlFile);
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
        $stringXmlParsed = $stringXml;
        $doc->loadXML($stringXmlParsed);
        $valid = $doc->validate();
        $algorismes = $doc->getElementsByTagName(self::$ALGORISME_PARAM);
        $node = new DOMNode;
        foreach ($algorismes as $algorisme) {
            if ($algorisme->firstChild->nodeValue == $className) {
                $node = $algorisme;
                break;
            }
        }
        $pnode = $node->parentNode;
        if ($pnode) {
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
    private function modifyPdeAlgorithm($className) {
        $response = self::$XML_ERROR_CODE;
        if ($this->removePdeAlgorithm($className)) {
            if ($this->addPdeAlgorithm($className)) {
                $response = self::$OK_CODE;
            } else {
                $response = self::$XML_ERROR_CODE;
            }
        } else {
            $response = self::$XML_ERROR_CODE;
        }
        return $response;
    }

    /**
     * Retorna el path del fitxer XML d'algorismes.
     * @return string Path to XML file.
     */
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
        return "../../../lib/_java/pde/algorismes/";
//        return DOKU_INC . $this->getConf('processingPdeRepository');
    }

    /**
     * Consulta el directori definit per les imatges de processing.
     * @global type $conf
     * @return type Retorna el directori definit per les imatges de processing.
     */
    private function getClassesRepositoryDir() {
        global $conf;
        return "../../../lib/_java/pde/classes/";
//        return DOKU_INC . $this->getConf('processingClassesRepository');
    }

    /**
     * Consulta el directori definit per les imatges de processing.
     * @global type $conf
     * @return type Retorna el directori definit per les imatges de processing.
     */
    private function getSrcRepositoryDir() {
        global $conf;
        return "../../../lib/_java/pde/src/";
//        return DOKU_INC . $this->getConf('processingSrcRepository');
    }

    private function getJavaLibDir() {
        global $conf;
        return "../../../lib/_java/lib/";
//        return DOKU_INC . $this->getConf('javaLib');
    }

}

?>
