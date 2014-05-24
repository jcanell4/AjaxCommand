<?php

/**
 * Description 
 *
 * @author Daniel Criado Casas
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
    private static $CODE_SAVE_FILE_INCORRECT=-1;
    private static $CODE_SAVE_FILE_CORRECT=1;
    private static $CODE_FILENAME_EXISTS=-2;
    private static $CODE_FILENAME_NOT_EXISTS=2;
    private static $CODE_COMMAND_UNDEFINED=-10;

    //Comandas
    private static $EXISTS_IMAGE_NAME_PARAM='existsImageName';
    private static $SAVE_IMAGE_PARAM='saveImage';
    private static $IMAGE_NAME_PARAM='imageName';

    //Parametres del fitxer
    private static $FILE_PARAM='file';
    private static $FILE_TYPE='image/png';

    public function __construct() {
        parent::__construct();
        if(@file_exists(DOKU_INC.'debug')){
            $this->authenticatedUsersOnly = false;
        }else{
            $this->authenticatedUsersOnly = true;
        }
    }

    protected function process() {
        //$response = $this->params;
        $response = self::$CODE_COMMAND_UNDEFINED;
        if (array_key_exists("do", $this->params)) {
            $do = $this->params["do"];
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

    protected function preprocess() {
        
    }

    protected function startCommand() {
        
    }

    protected function getDefaultResponse($response, &$ret) {
        $responseCode = $response;
        $info = "Info de proba";
//        switch ($responseCode) {
//            case self::$CODE_SAVE_FILE_INCORRECT:
//                $info = $this->getLang('save_file_incorrect');
//                break;
//            case self::$CODE_SAVE_FILE_CORRECT:
//                $info = $this->getLang('save_file_correct');
//                break;
//            case self::$CODE_FILENAME_EXISTS:
//                $info = $this->getLang('filename_exists');
//                break;
//            case self::$CODE_FILENAME_NOT_EXISTS:
//                $info = $this->getLang('filename_not_exists');
//                break;
//            case self::$CODE_COMMAND_UNDEFINED:
//                $info = $this->getLang('command_undefined');
//                break;
//            default:
//                $info = $this->getLang('unexpected_error');
//                break;
//        }
        $ret->addCodeTypeResponse($responseCode, $info);
    }

    /**
     * 
     * @return string
     */
    private function nameExists() {
        $response = "";
        if (array_key_exists(self::$IMAGE_NAME_PARAM, $this->params)) {
            $imageName = $this->params[self::$IMAGE_NAME_PARAM];
            $imagePath = $this->getRepositoryDir() . $nameImage;
            if (file_exists($imagePath)) {
                $response = self::$CODE_FILENAME_EXISTS;
            } else {
                $response = self::$CODE_FILENAME_NOT_EXISTS;
            }
        } else {
            $response = self::$CODE_COMMAND_UNDEFINED;
        }
        return $response;
    }

    /**
     * 
     * @return string
     */
    private function saveImage() {
        $response = self::$CODE_SAVE_FILE_INCORRECT;
        if (array_key_exists(self::$FILE_PARAM, $this->params)) {
            $file = $this->params[self::$FILE_PARAM];
            if ($file[self::$ERROR_PARAM] == 0 
                    && $file[self::$FILE_TYPE_PARAM] == self::$FILE_TYPE 
                    && is_uploaded_file($file[self::$FILE_CONTENT_PARAM])) {
                $nameImage = $file[self::$FILENAME_PARAM];
                $imagePath = $this->getRepositoryDir() . $nameImage;
                if(move_uploaded_file($file[self::$FILE_CONTENT_PARAM],$imagePath)){
                    $response = self::$CODE_SAVE_FILE_CORRECT;
                }
//                $contentImage = file_get_contents($file[self::$FILE_CONTENT_PARAM]);
//                if ($contentImage) {
//                    $nameImage = $file[self::$FILENAME_PARAM];
//                    $imagePath = $this->getRepositoryDir() . $nameImage;
//                    if (file_put_contents($imagePath, $contentImage)) {
//                        $response = self::$CODE_SAVE_FILE_CORRECT;
//                    }
//                }
            }
        }
        return $response;
    }
    
    private function getRepositoryDir(){
        global $conf;
        return $conf['mediadir'].$this->getConf('processingImageRepository');
    }
}

?>
