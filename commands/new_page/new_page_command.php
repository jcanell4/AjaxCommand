<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
if(!defined('DOKU_WIKIIOCMODEL')) define('DOKU_WIKIIOCMODEL', DOKU_PLUGIN . "wikiiocmodel/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once (DOKU_WIKIIOCMODEL.'DokuModelWrapper.php');

/**
 * Class page_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class new_page_command extends abstract_command_class {

    /**
     * El constructor estableix els tipus de 'id' i 'rev' i el valor per defecte de 'id' com a 'start'. i l'estableix
     * com a paràmetre.
     */
    public function __construct() {
        parent::__construct();
        $this->types['id'] = abstract_command_class::T_STRING;
        $this->permissionFor=  DokuModelAdapter::ADMIN_PERMISSION;
    }

    /**
     * Retorna la pàgina corresponent a la 'id' i 'rev'.
     *
     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        $contentData = $this->modelWrapper->getHtmlPage(
                                          $this->params['id'],
                                          $this->params['rev']
        );
        return $contentData;
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
        $responseGenerator->addHtmlDoc(
                          $contentData["id"], $contentData["ns"],
                          $contentData["title"], $contentData["content"]
        );
    }
}