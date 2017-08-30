<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");

require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_COMMAND . 'defkeys/PageKeys.php');

/**
 * Class edit_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class edit_partial_command extends abstract_command_class
{
    /**
     * Al constructor s'estableixen els tipus, els valors per defecte, i s'estableixen aquest valors com a paràmetres.
     */
    public function __construct()
    {
        parent::__construct();
        $this->types[PageKeys::KEY_ID] = abstract_command_class::T_STRING;
        $this->types[PageKeys::KEY_REV] = abstract_command_class::T_STRING;
        $this->types[PageKeys::KEY_RANGE] = abstract_command_class::T_STRING;
        $this->types[PageKeys::KEY_SUM] = abstract_command_class::T_STRING;
        $this->types[PageKeys::KEY_RECOVER_LOCAL_DRAFT] = abstract_command_class::T_BOOLEAN;
        $this->types[PageKeys::KEY_RECOVER_DRAFT] = abstract_command_class::T_BOOLEAN;
        $this->types[PageKeys::KEY_TO_REQUIRE] = abstract_command_class::T_BOOLEAN;
    }

    /**
     * Retorna el contingut de la página segons els paràmetres emmagatzemats en aquest command.
     *
     * @return array amb el contingut de la pàgina (id, ns, tittle i content)
     */
    protected function process()
    {

        if (strlen($this->params[PageKeys::KEY_IN_EDITING_CHUNKS]) == 0) {
            $this->params[PageKeys::KEY_EDITING_CHUNKS] = [];
        } else {
            $this->params[PageKeys::KEY_EDITING_CHUNKS] = explode(',', $this->params[PageKeys::KEY_IN_EDITING_CHUNKS]);
            $this->params[PageKeys::KEY_EDITING_CHUNKS][] = $this->params[PageKeys::KEY_SECTION_ID];
        }
        
        $contentData = $this->modelWrapper->getPartialEdit($this->params);
                

        return $contentData;
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

        $ret->addWikiCodeDoc(
            $response["id"], $response["ns"],
            $response["title"], $response["content"]
        );
    }
    
    public function getAuthorizationType() {
        return "edit";
    }

}