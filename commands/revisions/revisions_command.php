<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

/**
 * Class get_revisions_list_command.
 *
 * Ordre per gestionar la consulta i retorn de la llista de revisions pel document amb la id corresponent.
 *
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
class revisions_command extends abstract_command_class {

    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
    }

    /**
     * Retorna la resposta per defecte del command.
     *
     * @param mixed                    $response
     * @param AjaxCmdResponseGenerator $responseGenerator
     *
     * @return mixed
     */
    protected function getDefaultResponse($response, &$responseGenerator) {
        // TODO: Implement getDefaultResponse() method.
        $responseGenerator->addRevisionsTypeResponse($this->params['id'], $response);

    }

    /**
     * Retorna la llista de revisions.
     *
     * @return mixed - Llista de revisions del document.
     */
    protected function process() {
        return $this->modelWrapper->getRevisions($this->params['id']);

    }

    /**
     * Ens saltem la comprovació del token de seguretat
     */
    public function run($permission = NULL) {
        return $this->getResponse();
    }
}