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
 * Class edit_command
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class edit_command extends abstract_command_class {

	/**
	 * Al constructor s'estableixen els tipus, els valors per defecte, i s'estableixen aquest valors com a paràmetres.
	 */
	public function __construct() {
		parent::__construct();
		$this->types['id']      = abstract_command_class::T_STRING;
		$this->types['rev']     = abstract_command_class::T_STRING;
		$this->types['range']   = abstract_command_class::T_STRING;
		$this->types['summary'] = abstract_command_class::T_STRING;
	}

	/**
	 * Retorna el contingut de la página segons els paràmetres emmagatzemats en aquest command.
	 *
	 * @return array amb el contingut de la pàgina (id, ns, tittle i content)
	 */
	protected function process() {

		$draftExists = $this->getModelWrapper()->hasDraft($this->params['id']);

		$contentData = null;

		if ($draftExists && array_key_exists('recover_draft', $this->params)) {
			// Carreguem el draft
			$contentData = $this->_sendEditPageResponse($this->params['recover_draft']);

		} else if ($draftExists) {
			// Enviem el dialog, no la pàgina a editar
			$contentData = $this->_sendDraftDialogResponse();

		} else {
			// No hi ha draft, enviem el actual
			$contentData= $this->_sendEditPageResponse(false);
		}

		return $contentData;
	}

	private function _sendEditPageResponse($recover) {
		return $this->modelWrapper->getCodePage(
			$this->params['id'],
			$this->params['rev'],
			$this->params['range'],
			$this->types['summary'],
			$recover
		);
	}

	private function _sendDraftDialogResponse() {
		return $this->modelWrapper->getDraftDialog(
			$this->params['id'],
			$this->params['rev'],
			$this->params['range'],
			$this->types['summary']
		);
	}

	/**
	 * Afegeix la pàgina passada com argument com una resposta de tipus DATA_TYPE al generador de respostes.
	 *
	 * @param array                    $response amb el contingut de la pàgina
	 * @param AjaxCmdResponseGenerator $ret      objecte al que s'afegirà la resposta
	 *
	 * @return mixed|void
	 */
	protected function getDefaultResponse( $response, &$ret ) {

		$ret->addWikiCodeDoc(
			$response["id"], $response["ns"],
			$response["title"], $response["content"]
		);
	}
}