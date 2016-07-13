<?php
if (!defined('DOKU_INC')) {
    die();
}
if (!defined('DOKU_PLUGIN')) {
    define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
}
if (!defined('DOKU_COMMAND')) {
    define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
}
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_COMMAND . 'requestparams/PageKeys.php');
require_once(DOKU_COMMAND . 'requestparams/RequestParameterKeys.php');

/**
 * Class edit_command
 *
 * @author Xavier García <xaviergaro.dev#gmail.com>
 */
class project_command extends abstract_command_class
{

    /**
     * Al constructor s'estableixen els tipus, els valors per defecte, i s'estableixen aquest valors com a paràmetres.
     */
    public function __construct()
    {
        parent::__construct();
        $this->types[PageKeys::KEY_ID] = abstract_command_class::T_STRING;

        $this->types[RequestParameterKeys::DO_KEY] = abstract_command_class::T_STRING;
        $defaultValues = [RequestParameterKeys::DO_KEY => 'edit'];

        $this->setParameters($defaultValues);
    }

    protected function process()
    {

        switch ($this->params[RequestParameterKeys::DO_KEY]) {
            case 'edit':
                $projectMetaData = $this->modelWrapper->getProjectMetaData($this->params);
                break;

            case 'save':


                // TODO[Xavi] els 'name' dels camps arriben amb el format "aplanat", s'ha de reconstruir l'estructura
                $projectMetaData = $this->modelWrapper->setProjectMetaData($this->params);
                break;

            default:
                // TODO[Xavi] Llençar una excepció personlitzada, no existeix aquest 'do'.
                throw new Exception();
        }

        return $projectMetaData;
    }

    /**
     * Afegeix la pàgina passada com argument com una resposta de tipus DATA_TYPE al generador de respostes.
     *
     * @param array $response amb el contingut de la pàgina
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     *
     * @return mixed|void
     */
    protected function getDefaultResponse($response, &$ret)
    {

    }

    public function getAuthorizationType()
    {
        return "_none";
    }

}
