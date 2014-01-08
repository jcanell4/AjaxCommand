<?php
/**
 * Description of ResponseGenerator
 *
 * @author professor
 */
if(!defined('DOKU_INC')) define('DOKU_INC',dirname(__FILE__).'/../../');
require_once(DOKU_INC.'inc/JSON.php');

interface JsonGenerator{
    public function getJson();
    public function getJsonEncoded();    
}

class ResponseGenerator implements JsonGenerator{
    const HTML_TYPE=0;
    const TITLE_TYPE=1;
    const INFO_TYPE=2;
    const COMMAND_TYPE=3;
    const ERROR_TYPE=4;
    const LOGIN_INFO=5;
    const SECTOK_DATA=6;
    const DATA_TYPE=7;
    const META_INFO=8;
    const PROCESS_FUNCTION="process_function";
    const PROCESS_DOM_FROM_FUNCTION="process_dom_from_function"; //domId afectat + AMD (true/flase) + nom funcio/modul on es troba la funció + extra prams
    const CHANGE_DOM_STYLE="change_dom_style"; //domId afectat + propietat de l'estil a modificar + valor 
    const CHANGE_WIDGET_PROPERTY="change_widget_property"; //widgetId afectat + propietat a modificar + valor 
    const RELOAD_WIDGET_CONTENT="reaload_widget_content"; //widgetId afectat
    const ADD_WIDGET_CHILD="add_widget_child"; ////widgetId afectat + widgetId del fill a afegir + tipus de widget a crear + JSON amb els paràmetres per defecte
    const REMOVE_WIDGET_CHILD="remove_widget_child"; //widgetId afectat + widgetId del fill a eliminar
    const REMOVE_ALL_WIDGET_CHILDREN="remove_all_widget_children"; //widgetId afectat
    const JSINFO="jsinfo"; //informació per el javascrip
        
    private $value;
    private $type;
    private $encoder;
    
    public function __construct(/*integer*/ $type, $valueToSend) {
        $this->type = $type;
        $this->value=$valueToSend;
        $this->encoder=new JSON();
    }
    
    public function getJson(){
        //$arrayTypes = ResponseGenerator::TYPES;
        $arrayTypes = array("html", "title", "info", "command", "error"
                            ,"login", "sectok", "data", "metainfo");
        $data=array(
            "type" => $arrayTypes[$this->type],
            "value" => $this->value,
        );
        return $data;
    }
    
    public function getJsonEncoded(){
        $dataToEncode = $this->getJson();
        return $this->encoder->encode($dataToEncode); //json_encode($dataToEncode);
    }    
}

class ArrayJSonGenerator  implements JsonGenerator{
    private $items;
    private $encoder;
    
    public function __construct() {
        $this->encoder=new JSON();
    }

    public function getJson(){
        return $this->items;
    }
    
    public function getJsonEncoded(){
        $dataToEncode = $this->getJson();
        return $this->encoder->encode($this->items); //json_encode($this->items);
    }    

    public function add(/*JsonGenerator*/ $jSonGenerator){
        $this->items[]=$jSonGenerator->getJson();
    }
}

?>
