<?php
/**
 * [Rafa] Me sabe grave pero parece ser que este comando es un pobre huerfanito al que nadie llama
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_INC."lib/plugins/ajaxcommand/");
require_once(DOKU_COMMAND.'defkeys/ResponseParameterKeys.php');
require_once(DOKU_COMMAND.'defkeys/PageKeys.php');
require_once(DOKU_TPL_INCDIR . 'conf/cfgIdConstants.php');

/**
 * Class shortcuts_tab_command
 * @author Xavier Garcia <xaviergaro.dev@gmail.com>
 */
class shortcuts_tab_command extends abstract_command_class {
    /**
     * El constructor extableix el tipus, els valors per defecte i els estableix com a paràmetres.
     * El valor per defecte es el paràmetre 'do' amb valor 'admin'.
     */
    public function __construct() {
        parent::__construct();
        $this->authenticatedUsersOnly = TRUE;
        $this->types[PageKeys::KEY_DO] = self::T_STRING;
        $this->setParameters([PageKeys::KEY_DO => "shortcuts"]);
    }

    /**
     * Retorna la informació de la pestanya admin
     * @return array amb la informació de la pàgina formatada amb 'id', 'tittle' i 'content'
     */
    protected function process() {
        $user_id = $this->params[PageKeys::KEY_USER_ID];
        if (!$user_id) {
            throw new Exception("No es troba cap usuari al userinfo"); // TDOD[Xavi] canviar per una excepció més adient i localitzar el missatge.
        }else {
            $params = ['id' => WikiGlobalConfig::getConf('userpage_ns','wikiiocmodel').$user_id.':'.WikiGlobalConfig::getConf('shortcut_page_name','wikiiocmodel')];
        }
        $action = $this->getModelManager()->getActionInstance("ShortcutsTaskListAction");
        $contentData = $action->get($params);
        return $contentData;
    }

    /**
     * Afegeix el contingut com una resposta al generador de respostes passat com argument.
     * @param array $contentData array amb la informació de la pàgina 'id', 'tittle' i 'content'
     * @param AjaxCmdResponseGenerator $responseGenerator
     * @return void
     */
    protected function getDefaultResponse($contentData, &$responseGenerator) {
        //TO DO [JOSEP] Xavier, Aixo s'hauria de passar a una classe Shortcuts_tabResponseHandler,
        //perque el retorn no es neutre, implica que a la interficie hi ha un widget amb pestanyes
        //i aixo nomes ho pot saber el template!
        if($contentData["content"]){
            $containerClass = "ioc/gui/ContentTabNsTreeListFromPage";
            $urlBase = "lib/exe/ioc_ajax.php?call=page";
            $urlTree = "lib/exe/ioc_ajaxrest.php/ns_tree_rest/";

            $contantParams = array(
                PageKeys::KEY_ID => cfgIdConstants::TB_SHORTCUTS,
                "title" =>  $contentData['title'],
                "standbyId" => cfgIdConstants::BODY_CONTENT,
                "urlBase" => $urlBase,
                "data" => $contentData["content"],
                "treeDataSource" => $urlTree,
                'typeDictionary' => array (
                                      'p' => array (
                                                'urlBase' => "'lib/exe/ioc_ajax.php?call=project'",
                                                'params' => array (0 => PageKeys::PROJECT_TYPE),
                                             ),
                                    ),
            );
            $responseGenerator->addAddTab(cfgIdConstants::ZONA_NAVEGACIO,
                    $contantParams,
                    ResponseParameterKeys::FIRST_POSITION,
                    FALSE,
                    $containerClass);
        }else{
            $responseGenerator->addError(-1, "ShortcutsNotFound!");  //JOSEP: [TO DO] CANVIAR PER UNA EXCEPCIÓ QUAN ES PASSI AL RESPONSE HANDLER
        }
    }

    public function getAuthorizationType() {
        return "_none";
    }

}
