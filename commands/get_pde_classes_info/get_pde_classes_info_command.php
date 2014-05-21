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

class get_pde_classes_info_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->authenticatedUsersOnly = true;
    }

    protected function process() {
        $response = "";
        //TODO, agafar el fitxer xml i transformarlo en JSON.
        //Falta possar la ruta.
        if (file_exists('test.xml')) {
            $xml = simplexml_load_file('test.xml');
            $response = json_encode($sxml);
        } else {
            exit('Failed to open test.xml.');
        }
        return $response;
    }

    protected function preprocess() {
        
    }

    protected function startCommand() {
        
    }

    protected function getDefaultResponse($response, &$ret) {
        $ret->addInfoDta($response);
    }
}

?>
