<?php
if ( ! defined( 'DOKU_INC' ) ) {
	die();
}
if ( ! defined( 'DOKU_PLUGIN' ) ) {
	define( 'DOKU_PLUGIN', DOKU_INC . 'lib/plugins/' );
}
if ( ! defined( 'DOKU_COMMAND' ) ) {
	define( 'DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/" );
}
require_once( DOKU_COMMAND . 'AjaxCmdResponseGenerator.php' );
require_once( DOKU_COMMAND . 'JsonGenerator.php' );
require_once( DOKU_COMMAND . 'abstract_command_class.php' );

/**
 * Class diff_command
 *
 * @author Xavier García <xaviergaro.dev@gmail.com>
 */
class diff_command extends abstract_command_class {

	/**
	 * El constructor estableix els tipus de 'id' i 'rev' i el valor per defecte de 'id' com a 'start'. i l'estableix
	 * com a paràmetre.
	 */
	public function __construct() {
		parent::__construct();
		$this->types['id']  = abstract_command_class::T_STRING;
		$this->types['rev'] = abstract_command_class::T_STRING;
                $this->types['rev2'] = abstract_command_class::T_ARRAY;

		$defaultValues = array(
//			'id' => 'start',
		);
		$this->setParameters( $defaultValues );
	}

	/**
	 * Retorna la pàgina corresponent a la 'id' i 'rev'.
	 *
	 * @return array amb la informació de la pàgina formatada amb 'id', 'ns', 'tittle' i 'content'
	 */
	protected function process() {

		// TODO[Xavi] Al getDiffPage s'haura de pasar un array amb 2 valors amb el nom 'rev2' per comparar 2 revisions

		$contentData = $this->modelWrapper->getDiffPage(
			$this->params['id'],
			$this->params['rev'],
                        $this->params['rev2']
		);

        return $contentData;
    }

	/**
	 * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
	 *
	 * @param array                    $contentData array amb la informació de la pàgina 'id', 'ns', 'tittle' i
	 *                                              'content'
	 * @param AjaxCmdResponseGenerator $responseGenerator
	 *
	 * @return void
	 */
	protected function getDefaultResponse($response, &$ret) {
		global $lang;


		$ret->addInfoDta("info", $lang['draftdate'].' '.dformat(), $this->types['id']);
	}
}