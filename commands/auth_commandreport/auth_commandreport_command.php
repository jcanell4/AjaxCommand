<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

/**
 * Class auth_commandreport_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class auth_commandreport_command extends abstract_command_class {

    /**
     * Al constructor es defineix la propietat autheticatedUsersOnly com a cert.
     */
    public function __construct() {
        parent::__construct();
        $this->authenticatedUsersOnly = TRUE;
    }

    /**
     * Concatena els paràmetres separant-los amb una coma.
     *
     * @return string parametres concatenats
     */
    protected function process() {
        $response = (array("params" => array())); 
        foreach ($this->params as $key => $value) {
            if(is_array($value)){
                if($value["error"]==0 
                                && is_uploaded_file($value["tmp_name"])){
                    
                    $response["params"][$key]=array(
                            "filename" => $value["name"],
                            "type" => $value["type"],
                            "content" => file_get_contents($value["tmp_name"])
                     );
                }else{
                     $response["params"][$key]= "ERROR(".$value["error"].")";
                }
            }else{
                  $response["params"][$key]= $value;
            }
        }
        return $response;
//
//        $response = "params: ";
//        foreach($this->params as $key => $value) {
//            $response .= $key . ": " . $value . ", ";
//        }
//        $response = substr($response, 0, -2);
//
//        return $response;
    }

    protected function preprocess() {
    }

    protected function startCommand() {
    }

    /**
     * Afegeix una resposta de tipus INFO_TYPE al generador de respostes passat com argument.
     *
     * @param string                   $response informació per afegir
     * @param AjaxCmdResponseGenerator $ret      objecte al que s'afegirà la resposta
     *
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {
        $ret->addInfoDta($response);
    }
}