<?php
if(!defined('DOKU_INC')) die();
/**
 * Class auth_commandreport_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class auth_commandreport_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->authenticatedUsersOnly = TRUE;
    }

    /**
     * Concatena els paràmetres separant-los amb una coma.
     * @return string parametres concatenats
     */
    protected function process() {
        $response = (array("params" => array()));
        
        foreach ($this->params as $key => $value) {
            if (is_array($value)){
                if ($value["error"]==0 && is_uploaded_file($value["tmp_name"])) {
                    $response["params"][$key] = array(
                                                    "filename" => $value["name"],
                                                    "type" => $value["type"],
                                                    "content" => file_get_contents($value["tmp_name"])
                                                );
                }else{
                    $response["params"][$key] = "ERROR(".$value["error"].")";
                }
            }else{
                $response["params"][$key] = $value;
            }
        }
        return '<div>'.serialize($response)."</div>";
    }

    protected function preprocess() {}

    protected function startCommand() {}

    /**
     * Afegeix una resposta de tipus INFO_TYPE al generador de respostes passat com argument.
     * @param string                   $response informació per afegir
     * @param AjaxCmdResponseGenerator $ret      objecte al que s'afegirà la resposta
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {
        $ret->addInfoDta($response);
    }

    /**
     * @overwrite
     */
    public function getAuthorizationType() {
        return "_none";
    }
}