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

class copy_image_to_project_command extends abstract_command_class {

    /**Codi d'informació per quan no s'han pogut copiar totes les imatges.
     * @return integer Retorna un -1
     */
    private static $COPY_IMAGES_INCORRECT_CODE = -1;

    /**Codi d'informació per quan s'han copiat totes les imatges.
     * @return integer Retorna un 1
     */
    private static $COPY_IMAGES_CORRECT_CODE = 1;

    /**Codi d'informació per quan no arriba el directori del projecte.
     * @return integer Retorna un -10
     */
    private static $UNDEFINED_PROJECT_CODE = -10;
//    
 
    private static $PROJECT_PATH_PARAM = "projectPath";



    public function __construct() {
        parent::__construct();
        if (@file_exists(DOKU_INC . 'debug')) {
            $this->authenticatedUsersOnly = false;
        } else {
            $this->authenticatedUsersOnly = true;
        }

        $this->setParameters($defaultValues);
    }

    protected function process() {
        $response = self::$UNDEFINED_PROJECT_CODE;
        if (array_key_exists(self::$PROJECT_PATH_PARAM, $this->params)) {
            //El path del projecte ve separat per ':' en comptes de '/'
            $projectPath = str_replace(':', '/', $this->param[self::$PROJECT_PATH_PARAM]).'/';
            $imagesPath = $this->getImageRepositoryDir();
            foreach ($this->params as $key => $value) {
                if (strpos($key, "checkbox") == 0) {//el parametre es un checkbox d'una imatge
                    //QUE PASA SI JA EXISTEIX LA IMATGE?
                    $copied = copy($imagesPath.$value, $projectPath.$value);
                }
                if (!$copied) {
                    break;
                }
            }
            if ($copied){
                $response = self::$COPY_IMAGES_CORRECT_CODE;
            }else {
                $response = self::$COPY_IMAGES_INCORRECT_CODE;
            }
        }
        return $response;
    }

    protected function getDefaultResponse($response, &$ret) {
        $responseCode = $response;
        $info = "";
        switch ($responseCode) {
            case self::$COPY_IMAGES_INCORRECT_CODE:
                $info = $this->getLang('copyImagesIncorrect');
                break;
            case self::$COPY_IMAGES_CORRECT_CODE:
                $info = $this->getLang('copyImagesCorrect');
                break;
            case self::$UNDEFINED_PROJECT_CODE:
                $info = $this->getLang('undefinedProject');
                break;
            default:
                $info = $this->getLang('unexpectedError');
                break;
        }
        $ret->addCodeTypeResponse($responseCode, $info);
    }

    /**
     * Comprova si existeix el nom de la imatge.
     * @return integer Retorna el integer en referència al tipus de informació
     */
    private function nameExists() {
        $response = self::$UNDEFINED_COMMAND_CODE;
        if (array_key_exists(self::$IMAGE_NAME_PARAM, $this->params)) {
            $imageName = $this->params[self::$IMAGE_NAME_PARAM];
            $imagePath = $this->getImageRepositoryDir() . $imageName;
            if (file_exists($imagePath)) {
                $response = self::$FILENAME_EXISTS_CODE;
            } else {
                $response = self::$FILENAME_NOT_EXISTS_CODE;
            }
        }
        return $response;
    }

    /**
     * Desa la imatge en el directori correcte.
     * @return integer Retorna el integer en referència al tipus de informació
     */
    private function saveImage() {
        $response = self::$SAVE_FILE_INCORRECT_CODE;
        if (array_key_exists(self::$FILE_PARAM, $this->params)) {
            $file = $this->params[self::$FILE_PARAM];
            if ($file[self::$ERROR_PARAM] == UPLOAD_ERR_OK && $file[self::$FILE_TYPE_PARAM] == self::$PNG_MIME_TYPE && is_uploaded_file($file[self::$FILE_CONTENT_PARAM])) {
                $nameImage = $file[self::$FILENAME_PARAM];
                $imagePath = $this->getImageRepositoryDir() . $nameImage;
                $filePath = $file[self::$FILE_CONTENT_PARAM]; //path del fitxer temporal
                //Decodifica el fitxer
                $contentFile = base64_decode(file_get_contents($filePath));
                file_put_contents($filePath, $contentFile);
                if (file_exists($imagePath)) {//El nom de fitxer ja existeix
                    unlink($pathFile); //Elimina el fitxer temporal
                    $response = self::$FILENAME_EXISTS_CODE;
                } else {
                    if (move_uploaded_file($filePath, $imagePath)) {
                        $response = self::$SAVE_FILE_CORRECT_CODE;
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
    private function getImageRepositoryDir() {
        global $conf;
        return $conf['mediadir'] . $this->getConf('processingImageRepository');
    }

}

?>
