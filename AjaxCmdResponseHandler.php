<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AjaxCmdResponseHandler
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once DOKU_COMMAND.'JsonGenerator.php';

class AjaxCmdResponseHandler {
    const HTML_TYPE=0;
    const TITLE_TYPE=1;
    const INFO_TYPE=2;
    const COMMAND_TYPE=3;
    const ERROR_TYPE=4;
    const LOGIN_INFO=5;
    const SECTOK_DATA=6;
    const DATA_TYPE=7;
    const PROCESS_DOM_FROM_FUNCTION="process_dom_from_function"; //domId afectat + AMD (true/flase) + nom funcio/modul on es troba la funció + extra prams
    const CHANGE_DOM_STYLE="change_dom_style"; //domId afectat + propietat de l'estil a modificar + valor 
    const CHANGE_WIDGET_PROPERTY="change_widget_property"; //widgetId afectat + propietat a modificar + valor 
    const RELOAD_WIDGET_CONTENT="reaload_widget_content"; //widgetId afectat
    const ADD_WIDGET_CHILD="add_widget_child"; ////widgetId afectat + widgetId del fill a afegir + tipus de widget a crear + JSON amb els paràmetres per defecte
    const REMOVE_WIDGET_CHILD="remove_widget_child"; //widgetId afectat + widgetId del fill a eliminar
    const REMOVE_ALL_WIDGET_CHILDREN="remove_all_widget_children"; //widgetId afectat
    const JSINFO="jsinfo"; //informació per el javascrip
    
    private $response;
    
    public function __construct() {
        $this->response = new ArrayJSonGenerator();
    }
    
    public function addSetJsInfo($jsInfo){
        $this->response->add(
            new BasicJsonGenerator(
                AjaxCmdResponseHandler::COMMAND_TYPE, 
                array("type" => AjaxCmdResponseHandler::JSINFO,
                  "value" => $jsInfo,
                )
            )
        );                  
    }
    
    public function addProcessDomFromFunction(/*String*/ $domId, 
                                            /*Boolean*/ $isAmd, 
                                            /*String*/ $processName, 
                                            /*Array*/ $params){
        $this->response->add(
            new BasicJsonGenerator(
                AjaxCmdResponseHandler::COMMAND_TYPE, 
                array(
                    "type" => AjaxCmdResponseHandler::PROCESS_DOM_FROM_FUNCTION,
		    "id" => $domId, 
                    "amd" => $isAmd,
                    "processName" => $processName,
                    "params" => $params,
                )
            )
        );                          
    }

    public function addHtmlDoc($html){
        $this->response->add(new BasicJsonGenerator(
                AjaxCmdResponseHandler::HTML_TYPE, 
                $html)
        );
        
    }
    
    public function addWikiCodeDoc($code){
        $this->response->add(new BasicJsonGenerator(
                AjaxCmdResponseHandler::DATA_TYPE, 
                $code)
        );
        
    }
    
    public function addLoginInfo($loginInfo){
        $this->response->add(
            new BasicJsonGenerator(
                AjaxCmdResponseHandler::LOGIN_INFO,
		$loginInfo));	//afegir si és login(true) o logout(false)
        
    }

    public function addSectokData($data){
        $this->response->add(
                new BasicJsonGenerator(
                        AjaxCmdResponseHandler::SECTOK_DATA,
			$data));    
    }
    
    public function addChangeWidgetProperty(/*String*/ $widgetId, 
                                        /*String*/ $propertyName, 
                                                   $propertyValue){
        
        $this->response->add(
            new BasicJsonGenerator(
                AjaxCmdResponseHandler::COMMAND_TYPE, 
                array(
                    "type" => AjaxCmdResponseHandler::CHANGE_WIDGET_PROPERTY,
                    "id" => $widgetId, 
                    "propertyName" => $propertyName, 
                    "propertyValue" => $propertyValue)));              
    }
    
    public function addReloadWidgetContent(/*String*/ $widgetId){
        $this->response->add(
            new BasicJsonGenerator(
                AjaxCmdResponseHandler::COMMAND_TYPE, 
                array(
                    "type" => AjaxCmdResponseHandler::RELOAD_WIDGET_CONTENT,
                    "id" => $widgetId)));
        
    }
    
    public function addRemoveWidgetChild(/*String*/ $widgetId){
        $this->response->add(
            new BasicJsonGenerator(
                AjaxCmdResponseHandler::COMMAND_TYPE, 
                array(
                    "type" => AjaxCmdResponseHandler::REMOVE_WIDGET_CHILD,
                    "id" => $widgetId)));
        
    }

    public function addRemoveAllWidgetChildren(/*String*/ $widgetId){
        $this->response->add(
            new BasicJsonGenerator(
                AjaxCmdResponseHandler::COMMAND_TYPE, 
                array(
                    "type" => AjaxCmdResponseHandler::REMOVE_ALL_WIDGET_CHILDREN,
                    "id" => $widgetId)));
        
    }

    public function addInfoDta($info){
        $this->response->add(
            new BasicJsonGenerator(
                AjaxCmdResponseHandler::INFO_TYPE, 
                $info));
        
    }
    
}
?>
