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

    public function __construct() {

        parent::__construct();
        $this->authenticatedUsersOnly = false;
    }

    protected function process() {
        $response = $this->params;
        if (array_key_exists("do", $this->params)) {
            $do = $this->params["do"];
            print "$do \n";
            switch ($do) {
                case "askName":
                    print "askName\n";
                    $response = $this->nameExists();
                    break;
                case "saveImage":
                    print "saveImage\n";
                    $response = $this->saveImage();
                    break;
                default:
                    $response = "-1"; //do=S no esperat
                    break;
            }
        } else {
            $response = "-1";
        }

//        foreach ($this->params as $key => $value) {
//            if(is_array($value)){
//                if($value["error"]==0 
//                                && is_uploaded_file($value["tmp_name"])){
//                    $response .= $key."= {filename:".$value["name"];
//                    $response .= ", type:".$value["type"];
//                    $response .= ", content["
//                             .file_get_contents($value["tmp_name"])."]}, ";
//                }else{
//                    $response .= $key."= ERROR(".$value["error"]."), ";
//                }
//            }else{
//                $response .= $key."= ".$value.", ";
//            }
//        }
//        $response = substr($response, 0, -2);

        return $response;
    }

    protected function preprocess() {
        
    }

    protected function startCommand() {
        
    }

    protected function getDefaultResponse($response, &$ret) {
        $responseCode = $response;
        $info = "";
        switch ($responseCode) {
            case -3:
                $info = "";
                break;
            case -2:
                $info = "";
                break;
            case -1:
                $info = "";
                break;
            case 0:
                $info = "";
                break;
            case 1:
                $info = "";
                break;
            default:
                $info = "Error inesperado";
                break;
        }

        $ret->addInfoProcessCommand($responseCode, $info);
    }

    private function nameExists() {
        $response = "";
        $imageDir = "directori_imatges"; //TODO
        if (array_key_exists("imageName", $this->params)) {
            $imageName = $this->params["imageName"];
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . $imageDir . $imageName)) {
                $response = "-2";
            } else {
                $response = "0";
            }
        } else {
            $response = "-1";
        }
        return $response;
    }

    private function saveImage() {
        $response = "-3";
        foreach ($this->params as $key => $value) {
            if (is_array($value)) {
                //TODO
            }
        }
        return $response;
    }

}

?>
