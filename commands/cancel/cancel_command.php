<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once DOKU_PLUGIN . "ajaxcommand/requestparams/PageKeys.php";


/**
 * Class cancel_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class cancel_command extends abstract_command_class
{

    /**
     * Constructor per defecte que estableix el tipus id.
     */
    public function __construct()
    {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
        $this->types['auto'] = abstract_command_class::T_BOOLEAN;
        $this->types['auto'] = abstract_command_class::T_BOOLEAN;
        $this->types[PageKeys::KEY_DISCARD_DRAFT] = abstract_command_class::T_BOOLEAN;
        $this->types[PageKeys::KEY_KEEP_DRAFT] = abstract_command_class::T_BOOLEAN;
        $this->types[PageKeys::KEY_NO_RESPONSE] = abstract_command_class::T_BOOLEAN;
        
        $this->setParameterDefaultValues(array(PageKeys::KEY_NO_RESPONSE => FALSE));
    }

    /**
     * Cancela la edició.
     *
     * @return string[] array associatiu amb la resposta formatada (id, ns, tittle i content)
     */
    protected function process() {
        $contentData = $this->modelWrapper->cancelEdition($this->params);
        return $contentData;
    }

    /**
     * Afegeix una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     *
     * @param mixed $response // TODO[Xavi] No es fa servir per a res?
     * @param AjaxCmdResponseGenerator $ret objecte al que s'afegirà la resposta
     */
    protected function getDefaultResponse($contentData, &$ret)
    {
        //TODO[Xavi] $contentData no te cap valor?
        $ret->addHtmlDoc(
            $contentData["id"], $contentData["ns"],
            $contentData["title"], $contentData["content"]
        );
    }
}
