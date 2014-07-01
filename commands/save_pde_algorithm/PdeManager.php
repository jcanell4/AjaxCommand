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

require_once (DOKU_COMMAND_PDE . 'PdeJavaManager.php');
require_once (DOKU_COMMAND_PDE . 'PdeXmlManager.php');

class PdeManager {

    private $javaManager;
    private $xmlManager;
    private $command;
    private $params;

    function PdeManager($command) {
        $this->javaManager = new PdeJavaManager($command);
        $this->xmlManager = new PdeXmlManager($command);
        $this->command = $command;
    }

    function setParams($params) {
        $this->params = $params;
        $this->javaManager->setParams($params);
        $this->xmlManager->setParams($params);
    }

    /**
     * Informa sobre si un algorisme ja existeix.
     * @return int response code information
     */
    function existsAlgorithm() {
        $response = save_pde_algorithm_command::$UNDEFINED_ALGORITHM_NAME_CODE;
        if (array_key_exists(save_pde_algorithm_command::$ALGORITHM_NAME_PARAM, $this->params)) {
            $fileName = $this->params[save_pde_algorithm_command::$ALGORITHM_NAME_PARAM]; //fitxer pde
            $className = ucfirst(substr($fileName, 0, -4)); //Li treu la extensio .pde i capitalitza el string
            $response = $this->xmlManager->existsAlgorithm($className);
        }
        return $response;
    }

    /**
     * Modifica un algorisme ja existent.
     * @return int response code information
     */
    function modifyAlgorithm() {
        $response = save_pde_algorithm_command::$UNLOADED_ALGORITHM_CODE;
        if (array_key_exists(save_pde_algorithm_command::$FILE_PARAM, $this->params)) {

            $file = $this->params[save_pde_algorithm_command::$FILE_PARAM];
            $filePath = $file[save_pde_algorithm_command::$FILE_CONTENT_PARAM]; //path del fitxer temporal
            $fileName = $this->params[save_pde_algorithm_command::$ALGORITHM_NAME_PARAM]; //nomdelfitxer.pde
            $repositoryPdePath = $this->getPdeRepositoryDir();
            $pdePath = $repositoryPdePath . $fileName;
            if ($this->isPdeFile($file)) {
                if ($this->movePdeToRepository($filePath, $pdePath)) {
                    $className = ucfirst(substr($fileName, 0, -4)); //Li treu la extensio .pde i capitalitza el string
                    if ($this->javaManager->generateJavaClass($className, $pdePath)) {
                        $response = $this->xmlManager->modifyPdeAlgorithm($className);
                    } else {
                        $this->removePdeFromRepository($pdePath);
                        $response = save_pde_algorithm_command::$UNCOMPILED_ALGORITHM_CODE;
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
    function appendAlgorithm() {
        $response = save_pde_algorithm_command::$UNLOADED_ALGORITHM_CODE;
        //Validar fitxer perque sigui .pde, https://dojotoolkit.org/reference-guide/1.9/dojox/form/Uploader.html#missing-features
        if (array_key_exists(save_pde_algorithm_command::$FILE_PARAM, $this->params)) {
            $file = $this->params[save_pde_algorithm_command::$FILE_PARAM];
            $filePath = $file[save_pde_algorithm_command::$FILE_CONTENT_PARAM]; //path del fitxer temporal
            $fileName = $this->params[save_pde_algorithm_command::$ALGORITHM_NAME_PARAM]; //nomdelfitxer.pde
            $repositoryPdePath = $this->getPdeRepositoryDir();
            $pdePath = $repositoryPdePath . $fileName;
            if ($this->isPdeFile($file)) {
                //$this->command->modelWrapper->makeFileDir($pdePath); //assegura que el directori existeix
                if ($this->movePdeToRepository($filePath, $pdePath)) {
                    $className = ucfirst(substr($fileName, 0, -4)); //Li treu la extensio .pde i capitalitza el string
                    if ($this->javaManager->generateJavaClass($className, $pdePath)) {
                        if ($this->xmlManager->addPdeAlgorithm($className)) {
                            $response = save_pde_algorithm_command::$OK_CODE;
                        } else {
                            $response = save_pde_algorithm_command::$XML_ERROR_CODE;
                        }
                    } else {
                        $this->removePdeFromRepository($pdePath);
                        $response = save_pde_algorithm_command::$UNCOMPILED_ALGORITHM_CODE;
                    }
                }
            }
        }
        return $response;
    }

    /**
     * Consulta el directori definit per les imatges de processing.
     * @global type $conf
     * @return type Retorna el directori definit per les imatges de processing.
     */
    private function getPdeRepositoryDir() {
        return DOKU_INC . $this->command->getConf('processingPdeRepository');
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
        if ($file[save_pde_algorithm_command::$FILE_ERROR_PARAM] == UPLOAD_ERR_OK && $file[save_pde_algorithm_command::$FILE_TYPE_PARAM] == save_pde_algorithm_command::$PDE_MIME_TYPE && $this->endsWith($file[save_pde_algorithm_command::$FILENAME_PARAM], save_pde_algorithm_command::$PDE_EXTENSION) && is_uploaded_file($file[save_pde_algorithm_command::$FILE_CONTENT_PARAM])) {
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
    private function addPdeFromRepository($pdePath) {
        return unlink($pdePath);
    }

}
