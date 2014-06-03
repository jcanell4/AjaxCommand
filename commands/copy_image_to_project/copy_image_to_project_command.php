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
    //(0=OK, -1=UNAUTHORIZED, -2=OVER_WRITING_NOT_ALLOWED, 
    //-3=OVER_WRITING_UNAUTHORIZED, -5=FAILS, -4=WRONG_PARAMS
    //-6=BAD_CONTENT, -7=SPAM_CONTENT, -8=XSS_CONTENT)
    
    /**Codi d'informació que indica que el fitxer pot contenir contingut maligne
     * @return integer Retorna un -8
     */
    private static $XSS_CONTENT = -8;

    /**Codi d'informació que indica que el fitxer pot contenir spam
     * @return integer Retorna un -7
     */
    private static $SPAM_CONTENT = -7;

    /**Codi d'informació que indica que el contingut no es correspon amb el 
     * tipus declarat.
     * @return integer Retorna un -6
     */
    private static $BAD_CONTENT = -6;

    /**Codi d'informació per quan no s'han pogut copiar totes les imatges.
    /**Codi d'informació degut a que els paràmetres han agafat valors incorrectes.
     * @return integer Retorna un -5
     */
    private static $WRONG_PARAMS = -4;

    /**Codi d'informació per quan no s'han pogut copiar totes les imatges.
     * @return integer Retorna un -4
     */
    private static $COPY_FAILS = -5;

    /**Codi d'informació per quan s'intenta sobrescriure, però no es permet
     * @return integer Retorna un -3
     */
    private static $OVER_WRITING_UNAUTHORIZED = -3;

    /**Codi d'informació per quan s'intenta sobrescriure, però no es permet
     * @return integer Retorna un -2
     */
    private static $OVER_WRITING_NOT_ALLOWED = -2;

    /**Codi d'informació per quan s'han copiat totes les imatges.
     * @return integer Retorna un -1
     */
    private static $UNAUTHORIZED_USER = -1;

    /**Codi d'informació per quan s'han copiat totes les imatges.
     * @return integer Retorna un 0
     */
    private static $OK = 0;

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
                    //QUE PASA SI JA EXISTEIX LA IMATGE?  [TO DO]
                    $response = $this->modelWrapper->saveImage(
                            $this->param[self::$PROJECT_PATH_PARAM], 
                            $value, 
                            $imagesPath.$value, 
                            TRUE);
                }
                if ($response!=self::$UNDEFINED_PROJECT_CODE) {
                    break;
                }
            }
        }
        return $response;
    }

    protected function getDefaultResponse($response, &$ret) {     
        //$lang['uploadwrong']
        //$lang['uploadexist']
        //$lang['uploadbadcontent']
        //$lang['uploadspam']
        //$lang['uploadxss']
        //$lang['uploadfail']
        //$lang['uploadsucc']
        $responseCode = $response;
        $info = "";
        switch ($responseCode) {
            case self::$BAD_CONTENT:
                $info = $this->modelWrapper->getGlobalMessage('uploadbadcontent');
                break;
            case self::$COPY_FAILS:
                $info = $this->modelWrapper->getGlobalMessage('uploadfail');
                break;
            case self::$OK:
                $info = $this->modelWrapper->getGlobalMessage('uploadsucc');
                break;
            case self::$OVER_WRITING_UNAUTHORIZED:
                $info = $this->getLang('unauthorized_request');
                break;
            case self::$OVER_WRITING_NOT_ALLOWED:
                $info = $this->modelWrapper->getGlobalMessage('uploadexist');
                break;
            case self::$SPAM_CONTENT:
                $info = $this->modelWrapper->getGlobalMessage('uploadspam');
                break;
            case self::$UNAUTHORIZED_USER:
                $info = $this->getLang('unauthorized_request');
                break;
            case self::$WRONG_PARAMS:
                $info = $this->modelWrapper->getGlobalMessage('uploadwrong');
                break;
            case self::$XSS_CONTENT:
                $info = $this->modelWrapper->getGlobalMessage('uploadxss');
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

//    /**
//     * Comprova si existeix el nom de la imatge.
//     * @return integer Retorna el integer en referència al tipus de informació
//     */
//    private function nameExists() {
//        $response = self::$UNDEFINED_COMMAND_CODE;
//        if (array_key_exists(self::$IMAGE_NAME_PARAM, $this->params)) {
//            $imageName = $this->params[self::$IMAGE_NAME_PARAM];
//            $imagePath = $this->getImageRepositoryDir() . $imageName;
//            if (file_exists($imagePath)) {
//                $response = self::$FILENAME_EXISTS_CODE;
//            } else {
//                $response = self::$FILENAME_NOT_EXISTS_CODE;
//            }
//        }
//        return $response;
//    }
//
//    /**
//     * Desa la imatge en el directori correcte.
//     * @return integer Retorna el integer en referència al tipus de informació
//     */
//    private function saveImage() {
//        $response = self::$SAVE_FILE_INCORRECT_CODE;
//        if (array_key_exists(self::$FILE_PARAM, $this->params)) {
//            $file = $this->params[self::$FILE_PARAM];
//            if ($file[self::$ERROR_PARAM] == UPLOAD_ERR_OK && $file[self::$FILE_TYPE_PARAM] == self::$PNG_MIME_TYPE && is_uploaded_file($file[self::$FILE_CONTENT_PARAM])) {
//                $nameImage = $file[self::$FILENAME_PARAM];
//                $imagePath = $this->getImageRepositoryDir() . $nameImage;
//                $filePath = $file[self::$FILE_CONTENT_PARAM]; //path del fitxer temporal
//                //Decodifica el fitxer
//                $contentFile = base64_decode(file_get_contents($filePath));
//                file_put_contents($filePath, $contentFile);
//                if (file_exists($imagePath)) {//El nom de fitxer ja existeix
//                    unlink($pathFile); //Elimina el fitxer temporal
//                    $response = self::$FILENAME_EXISTS_CODE;
//                } else {
//                    if (move_uploaded_file($filePath, $imagePath)) {
//                        $response = self::$SAVE_FILE_CORRECT_CODE;
//                    }
//                }
//            }
//        }
//        return $response;
//    }

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
