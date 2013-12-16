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
    private $value;
    private $type;
    private $encoder;
    
    public function __construct(/*integer*/ $type, $valueToSend) {
        $this->type = $type;
        $this->value=$valueToSend;
        $this->encoder=new JSON();
    }
    
    public function getJson(){
        //$arrayTypes = AjaxCmdResponseHandler::TYPES;
        $arrayTypes = array("html", "title", "info", "command", "error"
                            , "login", "sectok", "data");
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
