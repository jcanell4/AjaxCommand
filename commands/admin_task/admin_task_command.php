<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');

/**
* Class admin_task_command
*
* @author Eduard Latorre
*/
class admin_task_command extends abstract_command_class {

  /**
  * El constructor extableix el tipus, els valors per defecte i els estableix
  * com a paràmetres.
  *
  * El valor per defecte es el paràmetre 'do' amb valor 'admin'.
  */
  public function __construct() {
    parent::__construct();
    $this->types['do'] = abstract_command_class::T_STRING;

    $defaultValues = array('do' => 'admin');
    $this->setParameters($defaultValues);
  }

  /**
  * Retorna la pàgina corresponent a la tasca d'administració 'page'.
  *
  * @return array amb la informació de la pàgina formatada amb 'id', 'tittle' i 'content'
  */
  protected function process() {
    $contentData = $this->modelWrapper->getAdminTask($this->params['page']);
    return $contentData;
  }


  /**
  * Afegeix el contingut com una resposta de tipus HTML_TYPE al generador de respostes passat com argument.
  *
  * @param array                    $contentData array amb la informació de la pàgina 'id', 'tittle' i 'content'
  * @param AjaxCmdResponseGenerator $responseGenerator
  *
  * @return void
  */
  protected function getDefaultResponse($contentData, &$responseGenerator) {
  }
}
