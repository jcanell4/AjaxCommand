<?php
/**
 * Description of BasicJsonGenerator
 *
 * @author professor
 */
if(!defined('DOKU_INC')) define('DOKU_INC',dirname(__FILE__).'/../../');
require_once(DOKU_INC.'inc/JSON.php');

interface JsonGenerator{
    public function getJson();
    public function getJsonEncoded();    
}

class BasicJsonGenerator implements JsonGenerator{
    const DATA_TYPE=0;
    const TITLE_TYPE=1;
    const INFO_TYPE=2;
    const COMMAND_TYPE=3;
    const ERROR_TYPE=4;
    const LOGIN_INFO=5;
    const SECTOK_DATA=6;
    const CHANGE_DOM_STYLE="change_dom_style"; //domId afectat + propietat de l'estil a modificar + valor 
    const CHANGE_WIDGET_PROPERTY="change_widget_property"; //widgetId afectat + propietat a modificar + valor 
    const RELOAD_WIDGET_CONTENT="reaload_widget_content"; //widgetId afectat
    const ADD_WIDGET_CHILD="add_widget_child"; ////widgetId afectat + widgetId del fill a afegir + tipus de widget a crear + JSON amb els paràmetres per defecte
    const REMOVE_WIDGET_CHILD="remove_widget_child"; //widgetId afectat + widgetId del fill a eliminar
    const REMOVE_ALL_WIDGET_CHILDREN="remove_all_widget_children"; //widgetId afectat
    private $value;
    private $type;
    private $encoder;
    
    public function __construct(/*integer*/ $type, $valueToSend) {
        $this->type = $type;
        $this->value=$valueToSend;
        $this->encoder=new JSON();
    }
    
    public function getJson(){
        //$arrayTypes = BasicJsonGenerator::TYPES;
        $arrayTypes = array("data", "title", "info", "command", "error"
                            , "login", "sectok");
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
