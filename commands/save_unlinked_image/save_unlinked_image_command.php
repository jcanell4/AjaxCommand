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

if (!defined('PROCESSING_IMAGE_REPOSITORY'))
    define('PROCESSING_IMAGE_REPOSITORY', $_SERVER["DOCUMENT_ROOT"] . '/dokuwiki/data/media/repository/pde/');

define('CODE_SAVE_FILE_INCORRECT', -1);
define('CODE_SAVE_FILE_CORRECT', 1);
define('CODE_FILENAME_EXISTS', -2);
define('CODE_FILENAME_NOT_EXISTS', 2);
define('CODE_COMMAND_UNDEFINED', -10);

//Comandas
define('EXISTS_IMAGE_NAME_PARAM', 'existsImageName');
define('SAVE_IMAGE_PARAM', 'saveImage');

//Parametres del fitxer
define('FILE_PARAM', 'file');
define('FILENAME_PARAM', 'name');
define('FILE_TYPE_PARAM', 'type');
define('FILE_TYPE', 'image/png');
define('ERROR_PARAM', 'error');
define('FILE_CONTENT_PARAM', 'tmp_name');

class save_unlinked_image_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->authenticatedUsersOnly = false;
    }

    protected function process() {
        //$response = $this->params;
        $response = CODE_COMMAND_UNDEFINED;
        if (array_key_exists("do", $this->params)) {
            $do = $this->params["do"];
            switch ($do) {
                case EXISTS_IMAGE_NAME_PARAM:
                    $response = $this->nameExists();
                    break;
                case SAVE_IMAGE_PARAM:
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
//            case CODE_SAVE_FILE_INCORRECT:
//                $info = $this->getLang('save_file_incorrect');
//                break;
//            case CODE_SAVE_FILE_CORRECT:
//                $info = $this->getLang('save_file_correct');
//                break;
//            case CODE_FILENAME_EXISTS:
//                $info = $this->getLang('filename_exists');
//                break;
//            case CODE_FILENAME_NOT_EXISTS:
//                $info = $this->getLang('filename_not_exists');
//                break;
//            case CODE_COMMAND_UNDEFINED:
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
        if (array_key_exists("imageName", $this->params)) {
            $imageName = $this->params["imageName"];
            $imagePath = PROCESSING_IMAGE_REPOSITORY . $nameImage;
            if (file_exists($imagePath)) {
                $response = CODE_FILENAME_EXISTS;
            } else {
                $response = CODE_FILENAME_NOT_EXISTS;
            }
        } else {
            $response = CODE_COMMAND_UNDEFINED;
        }
        return $response;
    }

    /**
     * 
     * @return string
     */
    private function saveImage() {
        $response = CODE_SAVE_FILE_INCORRECT;
        if (array_key_exists(FILE_PARAM, $this->params)) {
            $file = $this->params[FILE_PARAM];
            if ($file[ERROR_PARAM] == 0 && $file[FILE_TYPE_PARAM] == FILE_TYPE && is_uploaded_file($file[FILE_CONTENT_PARAM])) {
                $contentImage = file_get_contents($file[FILE_CONTENT_PARAM]);
                if ($contentImage) {
                    $nameImage = $file[FILENAME_PARAM];
                    $imagePath = PROCESSING_IMAGE_REPOSITORY . $nameImage;
                    if (file_put_contents($imagePath, $contentImage)) {
                        $response = CODE_SAVE_FILE_CORRECT;
                    }
                }
            }
        }
        return $response;
    }

}

?>
