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

class save_unlinked_image_command extends abstract_command_class {

    /**Codi d'informació per quan un fitxer no s'ha pogut dessar correctament.
     * @return integer Retorna un -1
     */
    private static $SAVE_FILE_INCORRECT_CODE = -1;
    
    /**Codi d'informació per quan un fitxer s'ha dessat correctament.
     * @return integer Retorna un 1
     */
    private static $SAVE_FILE_CORRECT_CODE = 1;
    
    /**Codi d'informació per quan un fitxer ja existeix.
     * @return integer Retorna un -2
     */
    private static $FILENAME_EXISTS_CODE = -2;
    
    /**Codi d'informació per quan un fitxer no existeix.
     * @return integer Retorna un 2
     */
    private static $FILENAME_NOT_EXISTS_CODE = 2;
    
    /**Codi d'informació per quan una comanda no estava definida.
     * @return integer Retorna un -10
     */
    private static $UNDEFINED_COMMAND_CODE = -10;
    
    //Comandas
    private static $COMMAND_PARAM = "do";
    private static $EXISTS_IMAGE_NAME_PARAM = 'existsImageName';
    private static $SAVE_IMAGE_PARAM = 'saveImage';
    private static $IMAGE_NAME_PARAM = 'imageName';
    
    //Parametres del fitxer
    private static $FILE_PARAM = 'file';
    private static $PNG_MIME_TYPE = 'image/png';

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
                case self::$EXISTS_IMAGE_NAME_PARAM:
                    $response = $this->nameExists();
                    break;
                case self::$SAVE_IMAGE_PARAM:
                    $response = $this->saveImage();
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
            case self::$SAVE_FILE_INCORRECT_CODE:
                $info = $this->getLang('saveFileIncorrect');
                break;
            case self::$SAVE_FILE_CORRECT_CODE:
                $info = $this->getLang('saveFileCorrect');
                break;
            case self::$FILENAME_EXISTS_CODE:
                $info = $this->getLang('filenameExists');
                break;
            case self::$FILENAME_NOT_EXISTS_CODE:
                $info = $this->getLang('filenameNotExists');
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
     * Comprova si existeix el nom de la imatge.
     * @return integer Retorna el integer en referència al tipus de informació
     */
    private function nameExists() {
        $response = self::$UNDEFINED_COMMAND_CODE;
        if (array_key_exists(self::$IMAGE_NAME_PARAM, $this->params)) {
            $imageName = $this->params[self::$IMAGE_NAME_PARAM];
            $imagePath = $this->getImageRepositoryDir() . $nameImage;
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
            if ($file[self::$ERROR_PARAM] == UPLOAD_ERR_OK 
                    && $file[self::$FILE_TYPE_PARAM] == self::$PNG_MIME_TYPE 
                    && is_uploaded_file($file[self::$FILE_CONTENT_PARAM])) {
                $nameImage = $file[self::$FILENAME_PARAM];
                $imagePath = $this->getImageRepositoryDir() . $nameImage;
                $filePath = $file[self::$FILE_CONTENT_PARAM];//path del fitxer temporal
                //Decodifica el fitxer
                $contentFile = base64_decode(file_get_contents($filePath));
                file_put_contents($filePath, $contentFile);
                if (file_exists($imagePath)) {//El nom de fitxer ja existeix
                    unlink($pathFile); //Elimina el fitxer temporal
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
