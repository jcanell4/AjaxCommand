<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");

require_once( DOKU_COMMAND . 'AjaxCmdResponseGenerator.php' );
require_once( DOKU_COMMAND . 'JsonGenerator.php' );
require_once( DOKU_COMMAND . 'abstract_command_class.php' );
require_once( DOKU_COMMAND . 'defkeys/PageKeys.php' );

/**
 * Class page_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class preview_command extends abstract_command_class {

    /**
     * El constructor estableix els tipus de 'id' i 'rev' i
     * el valor per defecte de 'id' com a 'start', i l'estableix com a paràmetre.
     */
    public function __construct() {
        parent::__construct();
	$this->types[PageKeys::KEY_ID]  = abstract_command_class::T_STRING;
	$this->types[PageKeys::KEY_TEXT] = abstract_command_class::T_STRING;
    }

    /**
     * Retorna la pàgina corresponent a la 'id' i 'rev'.
     *
     * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
     */
    protected function process() {
        $action = new PreviewAction();
        $ret = $action->get($this->params);
        return $ret;        
    }

    /**
     * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
     *
     * @param array                    $contentData array amb informació de la pàgina 'id', 'ns', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     *
     * @return void
     */
    protected function getDefaultResponse( $contentData, &$responseGenerator ) {

    }

    public function getAuthorizationType() {
        return "page";
    }
    
}
