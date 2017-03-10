<?php

if (!defined('DOKU_INC'))
    die();
if (!defined('DOKU_PLUGIN'))
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND'))
    define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once DOKU_PLUGIN . "ajaxcommand/requestparams/RequestParameterKeys.php";


/**
 * Class page_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class recent_command extends abstract_command_class {

    public function __construct(){
        parent::__construct();
        $this->types[RequestParameterKeys::ID_KEY] = abstract_command_class::T_STRING;
        $this->types[RequestParameterKeys::SHOW_CHANGES_KEY] = abstract_command_class::T_STRING;
        $this->types[RequestParameterKeys::FIRST_KEY] = abstract_command_class::T_ARRAY_KEY;
        
        $defaultValues = [
            RequestParameterKeys::ID_KEY => '',
            RequestParameterKeys::SHOW_CHANGES_KEY => 'both',
            RequestParameterKeys::FIRST_KEY => array(0 => ''),
        ];
        $this->setParameters($defaultValues);
    }


    protected function process() {
        $action = new RecentListAction($this->modelWrapper->getPersistenceEngine());
        $response = $action->get($this->params);
        return $response;
    }

    /**
     * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     *
     * @param array                    $contentData array amb la informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     *
     * @return void
     */
    protected function getDefaultResponse($contentData, &$responseGenerator) {        
    }

    /**
     * @return string (nom del command, a partir del nom de la clase,
     *                 modificat pels valors de $params per a definir subclasses específiques
     *                 amb autoritzacions específiques)
     */
    public function getAuthorizationType() {
        return "_none";
    }

}
