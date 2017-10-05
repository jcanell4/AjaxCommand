<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once (DOKU_PLUGIN . "ajaxcommand/defkeys/PageKeys.php");

/**
 * Class cancel_command
 *
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
class revision_command extends abstract_command_class
{

    /**
     * Constructor per defecte que estableix el tipus id.
     */
    public function __construct()
    {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
        $this->types['targetId'] = abstract_command_class::T_STRING;
        $this->types[PageKeys::KEY_OFFSET] = abstract_command_class::T_INTEGER;

    }

    /**
     * Cancela la edició.
     *
     * @return string[] array associatiu amb la resposta formatada (id, ns, tittle i content)
     */
    protected function process()
    {

        $response['revs'] =$this->modelWrapper->getRevisionsList($this->params);
        $response['revs']['urlBase'] = "lib/plugins/ajaxcommand/ajax.php?call=diff";
        $response['id'] = $this->params['targetId'];


        return $response;
    }

    /**
     * Afegeix una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     *
     * @param mixed $response
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     *
     * @return void
     */
    protected function getDefaultResponse($response, &$ret)
    {


        $ret->addRevisionsTypeResponse($response['id'], $response['revs']);

        //        $ret->addInfoDta($response['info']);
//
//        $id = $response['id'];
//        $ns = $response['ns'];
//        $timeout = $response['timeout'];
//
//        $ret->addRefreshLock($id, $ns, $timeout);
    }
}
