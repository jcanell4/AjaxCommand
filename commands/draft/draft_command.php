<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

/**
 * Class cancel_command
 *
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
class draft_command extends abstract_command_class {

    /**
     * Constructor per defecte que estableix el tipus id.
     */
    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
    }

    /**
     * Cancela la edició.
     *
     * @return string[] array associatiu amb la resposta formatada (id, ns, tittle i content)
     */
    protected function process() {
        return $this->modelWrapper->draft($this->params);
        
//        if($this->params["do"]==="save"){
//            $draft =json_decode($this->params['draft'], true);
//            return $this->modelWrapper->saveDraft($draft); // TODO[Xavi] Això hurà de contenir la info
//        }else if($this->params["do"]==="remove"){
//            return $this->modelWrapper->removeDraft($this->params); // TODO[Xavi] Això hurà de contenir la info
//        }else{
//            throw new UnexpectedValueException("Unexpected value '".$this->params["do"]."', for parameter 'do'");
//        }
    }

    /**
     * Afegeix una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     *
     * @param mixed                    $response
     * @param AjaxCmdResponseGenerator $ret      objecte al que s'afegirà la resposta
     *
     * @return void
     */
    protected function getDefaultResponse($response, &$ret) {

        if(isset($response['info'])){
            $ret->addInfoDta($response['info']);
        }else{
            $ret->addCodeTypeResponse(0);            
        }
    }
}
