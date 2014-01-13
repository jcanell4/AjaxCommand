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
    private $response;
    
    public function __construct() {
        $this->response = new ArrayJSonGenerator();
    }
    
    public function addSetJsInfo($jsInfo){
        $this->response->add(
            new ResponseGenerator(
                ResponseGenerator::COMMAND_TYPE, 
                array("type" => ResponseGenerator::JSINFO,
                  "value" => $jsInfo,
                )
            )
        );                  
    }
    
    public function addProcessFunction(/*Boolean*/ $isAmd, 
                                       /*String*/ $processName, 
                                       /*Any*/ $params){
        $this->response->add(
            new ResponseGenerator(
                ResponseGenerator::COMMAND_TYPE, 
                array(
                    "type" => ResponseGenerator::PROCESS_FUNCTION,
                    "amd" => $isAmd,
                    "processName" => $processName,
                    "params" => $params,
                )
            )
        );                          
    }

    public function addProcessDomFromFunction(/*String*/ $domId, 
                                            /*Boolean*/ $isAmd, 
                                            /*String*/ $processName, 
                                            /*Array*/ $params){
        $this->response->add(
            new ResponseGenerator(
                ResponseGenerator::COMMAND_TYPE, 
                array(
                    "type" => ResponseGenerator::PROCESS_DOM_FROM_FUNCTION,
		    "id" => $domId, 
                    "amd" => $isAmd,
                    "processName" => $processName,
                    "params" => $params,
                )
            )
        );                          
    }

    public function addHtmlDoc($html){
        $this->response->add(new ResponseGenerator(
                ResponseGenerator::HTML_TYPE, 
                $html)
        );
        
    }
    
    public function addWikiCodeDoc($code){
        $this->response->add(new ResponseGenerator(
                ResponseGenerator::DATA_TYPE, 
                $code)
        );
        
    }
    
    public function addLoginInfo($loginInfo){
        $this->response->add(
            new ResponseGenerator(
                ResponseGenerator::LOGIN_INFO,
		$loginInfo));	//afegir si és login(true) o logout(false)
        
    }

    public function addSectokData($data){
        $this->response->add(
                new ResponseGenerator(
                        ResponseGenerator::SECTOK_DATA,
			$data));    
    }
    
    public function addChangeWidgetProperty(/*String*/ $widgetId, 
                                        /*String*/ $propertyName, 
                                                   $propertyValue){
        
        $this->response->add(
            new ResponseGenerator(
                ResponseGenerator::COMMAND_TYPE, 
                array(
                    "type" => ResponseGenerator::CHANGE_WIDGET_PROPERTY,
                    "id" => $widgetId, 
                    "propertyName" => $propertyName, 
                    "propertyValue" => $propertyValue)));              
    }
    
    public function addReloadWidgetContent(/*String*/ $widgetId){
        $this->response->add(
            new ResponseGenerator(
                ResponseGenerator::COMMAND_TYPE, 
                array(
                    "type" => ResponseGenerator::RELOAD_WIDGET_CONTENT,
                    "id" => $widgetId)));
        
    }
    
    public function addRemoveWidgetChild(/*String*/ $widgetId){
        $this->response->add(
            new ResponseGenerator(
                ResponseGenerator::COMMAND_TYPE, 
                array(
                    "type" => ResponseGenerator::REMOVE_WIDGET_CHILD,
                    "id" => $widgetId)));
        
    }

    public function addRemoveAllWidgetChildren(/*String*/ $widgetId){
        $this->response->add(
            new ResponseGenerator(
                ResponseGenerator::COMMAND_TYPE, 
                array(
                    "type" => ResponseGenerator::REMOVE_ALL_WIDGET_CHILDREN,
                    "id" => $widgetId)));
        
    }

    public function addInfoDta($info){
        $this->response->add(
            new ResponseGenerator(
                ResponseGenerator::INFO_TYPE, 
                $info));
        
    }
    
    public function getResponse(){
        return $this->response->getJsonEncoded();
    }
    
}
?>
