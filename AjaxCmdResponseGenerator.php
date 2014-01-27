<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AjaxCmdResponseGenerator
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once DOKU_COMMAND.'JsonGenerator.php';

class AjaxCmdResponseGenerator {
    private $response;
    
    public function __construct() {
        $this->response = new ArrayJSonGenerator();
    }
    
    public function addResponse($response){
        $this->response->add($response);
    }
    
    public function addTitle($tit){
        $this->response->add(
            new JSonGeneratorImpl(JSonGenerator::TITLE_TYPE, $tit)
        );
    }
    
    public function addSetJsInfo($jsInfo){
        $this->response->add(
            new JSonGeneratorImpl(
                JSonGenerator::COMMAND_TYPE, 
                array("type" => JSonGenerator::JSINFO,
                  "value" => $jsInfo,
                )
            )
        );                  
    }
    
    public function addProcessFunction(/*Boolean*/ $isAmd, 
                                       /*String*/ $processName, 
                                       /*Any*/ $params=NULL){
        $resp = array(
                    "type" => JSonGenerator::PROCESS_FUNCTION,
                    "amd" => $isAmd,
                    "processName" => $processName,
                );
        if($params){
            $resp["params"]=$params;
        }
        $this->response->add(new JSonGeneratorImpl(JSonGenerator::COMMAND_TYPE
                                                    ,$resp));                          
    }

    public function addProcessDomFromFunction(/*String*/ $domId, 
                                            /*Boolean*/ $isAmd, 
                                            /*String*/ $processName, 
                                            /*Array*/ $params){
        $this->response->add(
            new JSonGeneratorImpl(
                JSonGenerator::COMMAND_TYPE, 
                array(
                    "type" => JSonGenerator::PROCESS_DOM_FROM_FUNCTION,
		    "id" => $domId, 
                    "amd" => $isAmd,
                    "processName" => $processName,
                    "params" => $params,
                )
            )
        );                          
    }
    
    public function addHtmlDoc($id, $ns, $title, $content){
        $contentData = array('id' => $id,
                                'ns' => $ns,
                                'title' => $title,
                                'content' => $content);
        $this->response->add(new JSonGeneratorImpl(
                JSonGenerator::HTML_TYPE, 
                $contentData)
        );
        
    }
    
    public function addWikiCodeDoc($id, $ns, $title, $content){
        $contentData = array('id' => $id,
                                'ns' => $ns,
                                'title' => $title,
                                'content' => $content);
        $this->response->add(new JSonGeneratorImpl(
                JSonGenerator::DATA_TYPE, 
                $contentData)
        );
        
    }
    
    public function addLoginInfo($loginRequest, $loginResul){
        $response=array("loginRequest" => $loginRequest
                ,"loginResult" => $loginResul);
        $this->response->add(
            new JSonGeneratorImpl(
                JSonGenerator::LOGIN_INFO,
		$response));	//afegir si és login(true) o logout(false)
        
    }

    public function addSectokData($data){
        $this->response->add(
                new JSonGeneratorImpl(
                        JSonGenerator::SECTOK_DATA,
			$data));    
    }
    
    public function addChangeWidgetProperty(/*String*/ $widgetId, 
                                        /*String*/ $propertyName, 
                                                   $propertyValue){
        
        $this->response->add(
            new JSonGeneratorImpl(
                JSonGenerator::COMMAND_TYPE, 
                array(
                    "type" => JSonGenerator::CHANGE_WIDGET_PROPERTY,
                    "id" => $widgetId, 
                    "propertyName" => $propertyName, 
                    "propertyValue" => $propertyValue)));              
    }
    
    public function addReloadWidgetContent(/*String*/ $widgetId){
        $this->response->add(
            new JSonGeneratorImpl(
                JSonGenerator::COMMAND_TYPE, 
                array(
                    "type" => JSonGenerator::RELOAD_WIDGET_CONTENT,
                    "id" => $widgetId)));
        
    }
    
    public function addRemoveWidgetChild(/*String*/ $widgetId, /*String*/ $childId){
        $this->response->add(
            new JSonGeneratorImpl(
                JSonGenerator::COMMAND_TYPE, 
                array(
                    "type" => JSonGenerator::REMOVE_WIDGET_CHILD,
                    "id" => $widgetId,
                    "childId" => $childId)));
        
    }

    public function addRemoveAllWidgetChildren(/*String*/ $widgetId){
        $this->response->add(
            new JSonGeneratorImpl(
                JSonGenerator::COMMAND_TYPE, 
                array(
                    "type" => JSonGenerator::REMOVE_ALL_WIDGET_CHILDREN,
                    "id" => $widgetId)));
        
    }

    public function addRemoveContentTab(/*String*/ $tabId){
        $this->response->add(
            new JSonGeneratorImpl(JSonGenerator::REMOVE_CONTENT_TAB, $tabId));
    }

    public function addRemoveAllContentTab(){
        $this->response->add(
            new JSonGeneratorImpl(JSonGenerator::REMOVE_ALL_CONTENT_TAB));
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

    public function addInfoDta($info){
        $this->response->add(
            new JSonGeneratorImpl(
                JSonGenerator::INFO_TYPE, 
                $info));
        
    }
    
    public function addMetadata($docId, $meta){
        $this->response->add(
                new JSonGeneratorImpl(JSonGenerator::META_INFO, 
                array(
                    "docId" => $docId,
                    "meta" => $meta,
                )));        
    }


    public function getResponse(){
        return $this->response->getJsonEncoded();
    }

    private function add($type, $data){
        $this->response->add(new JSonGeneratorImpl($type, $data));        
    }
}
?>
