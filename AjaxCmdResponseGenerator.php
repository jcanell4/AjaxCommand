<?php
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . 'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND', DOKU_PLUGIN . "ajaxcommand/");
require_once DOKU_COMMAND . 'JsonGenerator.php';

/**
 * Class AjaxCmdResponseGenerator
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
class AjaxCmdResponseGenerator {
    private $response;

    /**
     * Constructor de la classe on s'instancia el generador de respostes
     */
    public function __construct() {
        $this->response = new ArrayJSonGenerator();
    }

    /**
     * @param JsonGenerator $response
     */
    public function addResponse($response) {
        $this->response->add($response);
    }

    /**
     * Afegeix una resposta amb tipus ERROR_TYPE al generador de respostes.
     *
     * @param string $message missatge a afegir al generador de respostes
     */
    public function addError($c, $m=NULL) {
        if(is_string($c)){
            $value = array("code" => 0, "message" => $c);
        }else if(isset ($m)){
            $value = array("code" => $c, "message" => $m);
        }else{
            $value = $c;
        }
        $this->response->add(
                  new JSonGeneratorImpl(JSonGenerator::ERROR_TYPE, $value)
        );
    }

    /**
     * Afegeix una resposta amb tipus ERROR_TYPE al generador de respostes.
     *
     * @param string $message missatge a afegir al generador de respostes
     */
    public function addAlert($message) {
        $this->response->add(
                  new JSonGeneratorImpl(JSonGenerator::ALERT_TYPE, $message)
        );
    }

    /**
     * Afegeix una resposta amb tipus TITTLE_TYPE al generador de respostes.
     *
     * @param string $tit títol per afegir al generador de respostes
     */
    public function addTitle($tit) {
        $this->response->add(
                       new JSonGeneratorImpl(JSonGenerator::TITLE_TYPE, $tit)
        );
    }

    /**
     * Afegeix una resposta amb tipus COMMAND_TYPE::JSINFO al generador de respostes.
     *
     * @param string[] $jsInfo hash amb la informació que es pasarà al JavaScript
     */
    public function addSetJsInfo($jsInfo) {
        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::JSINFO,
                           $jsInfo
//                           array(
//                               "isadmin"  => $jsInfo['isadmin'],
//                               "ismanager" => $jsInfo['ismanager']
//                           )
                       )
        );
    }

    /**
     * Afegeix una resposta amb tipus COMMAND_TYPE::PROCESS_FUNCTION al generador de respostes
     *
     * @param bool       $isAmd
     * @param string     $processName
     * @param mixed|null $params
     */
    public function addProcessFunction($isAmd, $processName, $params = NULL) {
        $resp = array(
            "type"        => JSonGenerator::PROCESS_FUNCTION,
            "amd"         => $isAmd,
            "processName" => $processName,
        );

        if($params) {
            $resp["params"] = $params;
        }

        $this->response->add(
                       new JSonGeneratorImpl(JSonGenerator::COMMAND_TYPE, $resp)
        );
    }

    /**
     * Afegeix una resposta amb tipus COMMAND_TYPE::PROCESS_DOM_FROM_FUNCTION al generador de respostes.
     *
     * @param string $domId
     * @param bool   $isAmd
     * @param string $processName
     * @param array  $params
     */
    public function addProcessDomFromFunction($domId, $isAmd, $processName, $params) {
        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::COMMAND_TYPE,
                           array(
                               "type"        => JSonGenerator::PROCESS_DOM_FROM_FUNCTION,
                               "id"          => $domId,
                               "amd"         => $isAmd,
                               "processName" => $processName,
                               "params"      => $params,
                           )
                       )
        );
    }

    /**
     * Afegeix una resposta de tipus HTML_TYPE al generador de respostes.
     *
     * @param string $id
     * @param string $ns
     * @param string $title
     * @param string $content
     */
    public function addHtmlDoc($id, $ns, $title, $content) {
        $contentData = array(
            'id'      => $id,
            'ns'      => $ns,
            'title'   => $title,
            'content' => $content
        );

        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::HTML_TYPE,
                           $contentData)
        );
    }

        /**
     * Afegeix una resposta de tipus MEDIA_TYPE al generador de respostes.
     *
     * @param string $id
     * @param string $ns
     * @param string $title
     * @param string $content
     */
    public function addMedia($id, $ns, $title, $content) {
        $contentData = array(
            'id'      => $id,
            'ns'      => $ns,
            'title'   => $title,
            'content' => $content
        );

        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::MEDIA_TYPE,
                           $contentData)
        );
    }
    
   /**
     * Afegeix una resposta de tipus MEDIADETAILS_TYPE al generador de respostes.
     *
     * @param string $id
     * @param string $ns
     * @param string $title
     * @param string $content
     */
    public function addMediaDetails($id, $ns, $title, $content) {
        $contentData = array(
            'id'      => $id,
            'ns'      => $ns,
            'title'   => $title,
            'content' => $content
        );

        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::MEDIADETAILS_TYPE,
                           $contentData)
        );
    }
    

    /**
     * Afegeix una resposta de tipus DATA_TYPE al generador de respostes.
     *
     * @param string $id
     * @param string $ns
     * @param string $title
     * @param string $content
     * @param string[] $editing - Editing params
     */
    public function addWikiCodeDoc($id, $ns, $title, $content, $editing) {
        $contentData = array(
            'id'      => $id,
            'ns'      => $ns,
            'title'   => $title,
            'content' => $content,
	        'editing' => $editing
        );

        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::DATA_TYPE,
                           $contentData)
        );
    }

    /**
     * Afegeix una resposta de tipus LOGIN_INFO al generador de respostes.
     *
     * @param string $loginRequest
     * @param string $loginResult
     */
    public function addLoginInfo($loginRequest, $loginResult, $userId=NULL) {
        $response = array(
            "loginRequest" => $loginRequest,
            "loginResult"  => $loginResult
        );
        if($userId){
            $response["userId"] = $userId;
        }

        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::LOGIN_INFO,
                           $response)
        ); //afegir si és login(true) o logout(false)
    }

    /**
     * Afegeix una resposta de tipus SECTOK_DATA al generador de respostes.
     *
     * @param string $data dades del token de seguretat
     */
    public function addSectokData($data) {
        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::SECTOK_DATA,
                           $data)
        );
    }

    /**
     * Afegeix una resposta de tipus COMMAND_TYPE::CHANGE_WIDGET_PROPERTY
     *
     * @param string $widgetId
     * @param string $propertyName
     * @param mixed  $propertyValue
     */
    public function addChangeWidgetProperty($widgetId, $propertyName, $propertyValue) {
        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::COMMAND_TYPE,
                           array(
                               "type"          => JSonGenerator::CHANGE_WIDGET_PROPERTY,
                               "id"            => $widgetId,
                               "propertyName"  => $propertyName,
                               "propertyValue" => $propertyValue
                           ))
        );
    }

    /**
     * Afegeix una resposta de tipus COMMAND_TYPE::RELOAD_WIDGET_CONTENT al generador de respostes.
     *
     * @param string $widgetId
     */
    public function addReloadWidgetContent($widgetId) {
        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::COMMAND_TYPE,
                           array(
                               "type" => JSonGenerator::RELOAD_WIDGET_CONTENT,
                               "id"   => $widgetId
                           ))
        );
    }

    /**
     * Afegeix una resposta de tipus COMMAND_TYPE::REMOVE_WIDGET_CHILD al generador de respostes.
     *
     * @param string $widgetId
     * @param string $childId
     */
    public function addRemoveWidgetChild($widgetId, $childId) {
        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::COMMAND_TYPE,
                           array(
                               "type"    => JSonGenerator::REMOVE_WIDGET_CHILD,
                               "id"      => $widgetId,
                               "childId" => $childId
                           ))
        );
    }

    /**
     * Afegeix una resposta de tipus COMMAND_TYPE::REMOVE_ALL_WIDGET_CHILDREN al generador de respostes.
     *
     * @param string $widgetId
     */
    public function addRemoveAllWidgetChildren($widgetId) {
        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::COMMAND_TYPE,
                           array(
                               "type" => JSonGenerator::REMOVE_ALL_WIDGET_CHILDREN,
                               "id"   => $widgetId
                           ))
        );
    }

    /**
     * Afegeix una resposta de tipus REMOVE_CONTENT_TAB al generador de respostes.
     *
     * @param string $tabId
     */
    public function addRemoveContentTab($tabId) {
        $this->response->add(
                       new JSonGeneratorImpl(JSonGenerator::REMOVE_CONTENT_TAB, $tabId)
        );
    }

    /**
     * Afegeix una resposta de tipus REMOVE_ALL_CONTENT_TAB al generador de respostes.
     */
    public function addRemoveAllContentTab() {
        $this->response->add(
                       new JSonGeneratorImpl(JSonGenerator::REMOVE_ALL_CONTENT_TAB)
        );
    }

//    public function addRemoveMetaTab(/*String*/ $tabId){
//        $this->response->add(
//            new JSonGeneratorImpl(
//                JSonGenerator::COMMAND_TYPE,
//                array(
//                    "type" => JSonGenerator::REMOVE_META_TAB,
//                    "id" => $tabId)));
//
//    }
//
//    public function addRemoveAllMetaTab(/*String*/ $widgetId){
//        $this->response->add(
//            new JSonGeneratorImpl(
//                JSonGenerator::COMMAND_TYPE,
//                array(
//                    "type" => JSonGenerator::REMOVE_ALL_META_TAB,
//                    "id" => $widgetId)));
//
//    }

    /**
     * Afegeix una resposta de tipus INFO_TYPE al generador de respostes.
     *
     * @param string $info
     */ //$type, $message, $id = null, $duration = -1)
    public function addInfoDta($info, $message=null, $id = null, $duration = -1, $timestamp="") {
        if($message){
            $resp = array(
                "id" => $id,
                "type" => $info,
                "message" => $message,
                "duration" => $duration,
                "timestamp" => $timestamp);
        }else{
            $resp=$info;
        }
        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::INFO_TYPE,
                           $resp)
        );
    }

    /**
     * Afegeix una resposta de tipus CODE_TYPE_RESPONSE al generador de respostes.
     *
     * @param int    $responseCode
     * @param string $info
     */
    public function addCodeTypeResponse($responseCode, $info = "") {
        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::CODE_TYPE_RESPONSE,
                           array(
                               "code" => $responseCode,
                               "info" => $info,
                           ))
        );
    }

    /**
     * Afegeix una resposta de tipus SIMPLE_TYPE_RESPONSE al generador de respostes.
     *
     * @param $return
     */
    public function addSimpleTypeResponse($return) {
        $this->add(JSonGenerator::SIMPLE_TYPE_RESPONSE, $return);
    }

    /**
     * Afegeix una resposta de tipus ARRAY_TYPE_RESPONSE al generador de respostes.
     *
     * @param array $return
     */
    public function addArrayTypeResponse($return) {
        $this->add(JSonGenerator::ARRAY_TYPE_RESPONSE, $return);
    }

    /**
     * Afegeix una resposta de tipus ARRAY_TYPE_RESPONSE al generador de respostes.
     *
     * @param object $return
     */
    public function addObjectTypeResponse($return) {
        $this->add(JSonGenerator::OBJECT_TYPE_RESPONSE, $return);
    }

    /**
     * Afegeix una resposta de tipus META_INFO al generador de respostes.
     *
     * @param string   $id
     * @param string[] $meta hash amb les metadades
     */
    public function addMetadata($id, $meta) {
        $this->response->add(
                       new JSonGeneratorImpl(JSonGenerator::META_INFO,
                                             array(
                                                 "id" => $id,
                                                 "meta"  => $meta,
                                             ))
        );
    }
        /**
     * Afegeix una resposta de tipus META_MEDIA_INFO al generador de respostes.
     *
     * @param string   $docId
     * @param string[] $meta hash amb les metadades
     */
    public function addMetaMediaData($docId, $meta) {
        $this->response->add(
                       new JSonGeneratorImpl(JSonGenerator::META_MEDIA_INFO,
                                             array(
                                                 "docId" => $docId,
                                                 "meta"  => $meta,
                                             ))
        );
    }

    /**
     * Retorna una cadena en format JSON amb totes les respostes codificades.
     *
     * @return string resposta codificada en JSON
     */
    public function getResponse() {
        return $this->response->getJsonEncoded();
    }

    /**
     * Afegeix una resposta del tipus especificat amb les dades passades com argument al generador de respostes.
     *
     * @param int   $type
     * @param mixed $data
     */
    private function add($type, $data) {
        $this->response->add(new JSonGeneratorImpl($type, $data));
    }

    /**
    * Afegeix una resposta de tipus ADMIN_TAB al generador de respostes.
    *
    * @param string $containerId    identificador del contenidor on afegir la pestanya
    * @param string $tabId          identificador de la pestanya
    * @param string $title          títol de la pestanya
    * @param string $content        contingut html amb la llista de tasques
    * @param string $urlBase        urlBase de la comanda on dirigir les peticions de cada tasca
    */
    public function addAdminTab($containerId, $tabId, $title, $content, $urlBase) {
        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::ADMIN_TAB,
                           array(
                               "type" => JSonGenerator::ADD_ADMIN_TAB,
                               "containerId" => $containerId,
                               "tabId" => $tabId,
                               "title" => $title,
                               "content" => $content,
                               "urlBase" => $urlBase
                           ))
        );
    }

    public function addRemoveAdminTab($containerId, $tabId, $urlBase) {
        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::ADMIN_TAB,
                           array(
                               "type" => JSonGenerator::REMOVE_ADMIN_TAB,
                               "containerId" => $containerId,
                               "tabId" => $tabId,
                               "urlBase" => $urlBase
                           ))
        );
    }

    /**
     * Afegeix una resposta de tipus ADMIN_TASK al generador de respostes.
     *
     * @param string $id
     * @param string $ns
     * @param string $title
     * @param string $content
     */
    public function addAdminTask($id, $ns, $title, $content) {

        $this->response->add(
                       new JSonGeneratorImpl(
                           JSonGenerator::ADMIN_TASK,
                           array(
                               'id'      => $id,
                               'ns'      => $ns,
                               'title'   => $title,
                               'content' => $content
                           ))
        );
    }

    /**
     * Afegeix una resposta de tipus REVISIONS al generador de respostes.
     *
     * @param $id
     * @param $revisions
     *
     */
    public function addRevisionsTypeResponse($id, $revisions) {
        $this->add(JSonGenerator::REVISIONS_TYPE, array (
            'id' => $id,
            'revisions' => $revisions));
    }
}
