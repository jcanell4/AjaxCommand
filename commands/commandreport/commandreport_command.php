<?php
if(!defined('DOKU_INC')) die();
/**
 * Class commandreport_command
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class commandreport_command extends abstract_command_class {

    /**
     * Al constructor s'estableix que no es necessari que l'usuari estigui autenticat.
     */
    public function __construct() {
        parent::__construct();
        $this->authenticatedUsersOnly = FALSE;
    }

    /**
     * TODO[Xavi] No trobo on es crida aquesta classe, no entenc que fa a banda de generar la resposta segons si s'ha
     * pujat un fitxer o s'han enviat paràmetres.
     * @return array|mixed
     */
    protected function process() {
        $params = (array("params" => array()));

        foreach ($this->params as $key => $value) {
            if (is_array($value)){
                if ($value["error"]==0 && is_uploaded_file($value["tmp_name"])) {
                    $params["params"][$key] = array(
                                                "filename" => $value["name"],
                                                "type" => $value["type"],
                                                "content" => file_get_contents($value["tmp_name"])
                                            );
                }else{
                    $params["params"][$key]= "ERROR(".$value["error"].")";
                }
            }else{
                $params["params"][$key]= $value;
            }
        }
        return '<div>'.serialize($params)."</div>";
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
        $ret->addInfoDta("info", $response, null, -1, \date('d-m-Y H:i:s'));
    }

    /**
     * @overwrite
     */
    public function getAuthorizationType() {
        return "_none";
    }
}
