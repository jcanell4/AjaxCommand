<?php

if(!defined("DOKU_INC")) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
require_once(DOKU_PLUGIN . 'ajaxcommand/AjaxCmdResponseGenerator.php');

/**
 * Class AbstractResponseHandler
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
abstract class AbstractResponseHandler {
    const LOGIN  = 'login';
    const PAGE   = 'page';
    const EDIT   = 'edit';
    const CANCEL = 'cancel';
    const SAVE   = 'save';
    const MEDIA  = 'media';
    const MEDIADETAILS  = 'mediadetails';    
    const ADMIN_TASK  = 'admin_task';
    const ADMIN_TAB  = 'admin_tab';

    private $cmd;
    private $modelWrapper;
    private $authorization;

    /**
     * Constructor al que se li passa el nom del Command com argument.
     *
     * @param string $cmd
     */
    public function __construct($cmd, $modelWrapper=NULL, $authorization=NULL) {
        $this->cmd = $cmd;
        if($modelWrapper){
            $this->modelWrapper = $modelWrapper;
        }
        if($authorization){
            $this->authorization = $authorization;
        }
    }

    /**
     * @return string
     */
    public function getCommandName() {
        return $this->cmd;
    }

    /**
     * @return ModelWrapper instance
     */
    public function getModelWrapper() {
        return $this->modelWrapper;
    }

    /**
     * Set ModelWrapper instance
     */
    public function setModelWrapper($modelWrapper) {
        $this->modelWrapper=$modelWrapper;
    }

    /**
     * @return Authorization instance
     */
    public function getAuthorization() {
        return $this->authorization;
    }

    /**
     * Set Authorization instance
     */
    public function setAuthorization($authorization) {
        $this->authorization = $authorization;
    }

    /**
     * Processa la resposta cridant abans a preResponse() i després de processar-la a postResponse().
     *
     * @param string[]                 $requestParams hash amb els paràmetres
     * @param mixed                    $responseData  dades per processar
     * @param AjaxCmdResponseGenerator $ajaxCmdResponseGenerator
     */
    public function processResponse($requestParams,
                                    $responseData,
                                    &$ajaxCmdResponseGenerator) {
        $this->preResponse($requestParams, $ajaxCmdResponseGenerator);
        $this->response($requestParams, $responseData, $ajaxCmdResponseGenerator);
        $this->postResponse($requestParams, $responseData, $ajaxCmdResponseGenerator);
    }

    /**
     * Codi per executar quan es processa la resposta.
     *
     * @param string[]                 $requestParams hash amb els paràmetres
     * @param mixed                    $responseData  dades per processar
     * @param AjaxCmdResponseGenerator $ajaxCmdResponseGenerator
     *
     * @return mixed
     */
    protected abstract function response($requestParams, $responseData,
                                         &$ajaxCmdResponseGenerator);

    /**
     * Codi per executar abans de processar la resposta.
     *
     * @param string[]                 $requestParams hash amb els paràmetres
     * @param AjaxCmdResponseGenerator $ajaxCmdResponseGenerator
     *
     * @return mixed
     */
    protected abstract function preResponse($requestParams,
                                            &$ajaxCmdResponseGenerator);

    /**
     * Codi per executar despres de processar la resposta.
     *
     * @param string[]                 $requestParams hash amb els paràmetres
     * @param mixed                    $responseData  dades per processar
     * @param AjaxCmdResponseGenerator $ajaxCmdResponseGenerator
     *
     * @return mixed
     */
    protected abstract function postResponse($requestParams, $responseData,
                                             &$ajaxCmdResponseGenerator);
}
