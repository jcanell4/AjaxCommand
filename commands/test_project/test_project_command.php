<?php
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once(DOKU_COMMAND . 'AjaxCmdResponseGenerator.php');
require_once(DOKU_COMMAND . 'JsonGenerator.php');
require_once(DOKU_COMMAND . 'abstract_command_class.php');
require_once(DOKU_COMMAND . 'requestparams/ResponseParameterKeys.php');

/**
 * Class admin_tab_command crea la pestanya admin
 *
 * @author Xavier Garcia <xaviergaro.dev@gmail.com>
 */
class test_project_command extends abstract_command_class
{

    /**
     * El constructor extableix el tipus, els valors per defecte i els estableix
     * com a paràmetres.
     *
     * El valor per defecte es el paràmetre 'do' amb valor 'admin'.
     */
    public function __construct()
    {
        parent::__construct();

        $this->authenticatedUsersOnly = TRUE;
        $this->types['do'] = abstract_command_class::T_STRING;
    }

    /**
     * Retorna la informació de la pestanya admin
     *
     * @return array amb la informació de la pàgina formatada amb 'id', 'tittle' i 'content'
     */
    protected function process()
    {
//        // TODO[Xavi] Aqui s'ha d'obtenir la pàgina de les dreceres de l'usuari
//        $contentData = $this->getModelWrapper()->getShortcutsTaskList($this->params[PageKeys::KEY_USER_ID]);

        $contentData = [
            'title' => 'Projecte', // TODO[Xavi] Localitzar
            'id' => $this->params[pagekeys::KEY_ID], // ALERTA[Xavi] Un projecte pot estar dintre d'una carpeta? llavors s'hauria de fer la substitució dels : per _
            'ns' => $this->params[pagekeys::KEY_ID]
        ];

        return $contentData;
    }


    /**
     * Afegeix el contingut com una resposta al generador de respostes passat com argument.
     *
     * @param array $contentData array amb la informació de la pàgina 'id', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     *
     * @return void
     */
    protected function getDefaultResponse($contentData, &$responseGenerator)
    {
        return null; // Es requereix el responseHandler del tpl
    }


    public function getAuthorizationType()
    {
        return "_none";
    }

}
