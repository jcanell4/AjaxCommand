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
        $this->authenticatedUsersOnly = true;
    }

    protected function process() {
        $response = "-1";
        if (array_key_exists("do", $this->params)) {
            $do = $this->params["do"];
            print "$do \n";
            switch ($do) {
                case "existImageName":
                    print "existImageName\n";
                    $response = $this->nameExists();
                    break;
                case "saveImage":
                    print "saveImage\n";
                    $response = $this->saveImage();
                    break;
                default:
                    $response = "-1"; //do=X no esperat
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
        $info = "";
        switch ($responseCode) {
            case -3:
                $info = "No s'ha pogut dessar el fitxer";
                break;
            case -2:
                $info = "El nom del fitxer ja existeix";
                break;
            case -1:
                $info = "Comanda no definida";
                break;
            case 0:
                $info = "Nom del fitxer disponible";
                break;
            case 1:
                $info = "Fitxer dessat correctament";
                break;
            default:
                $info = "Error inesperat";
                break;
        }
        $ret->addInfoProcessCommand($responseCode, $info);
    }

    /**
     * 
     * @return string
     */
    private function nameExists() {
        $response = "";
        $imageDir = "data/media/repository/pde"; //TODO
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

    /**
     * 
     * @return string
     */
    private function saveImage() {
        $response = "-3";
        if (array_key_exists("file", $this->params)) {
            $value = $this->params;
            print "fitxer trobat\n";
            if ($value["error"] == 0 && $value["type"] == "image/png" && is_uploaded_file($value["tmp_name"])) {
                $nameImage = $value["filename"];
                $contentImage = file_get_contents($value["tmp_name"]);
                if ($contentImage) {
                    $dirImages = "data/media/repository/pde";
                    if (file_put_contents($dirImages + $nameImage, $contentImage)) {
                        print "He guardat el fitxer \n";
                        $response = "1";
                    }
                }
            }
        }
        return $response;
    }

}

?>
