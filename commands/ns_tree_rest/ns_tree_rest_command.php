<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
//if(!defined('CURRENT_NODE_NS_TREE_PARAM')) define('CURRENT_NODE_NS_TREE_PARAM', 1);
require_once(DOKU_INC.'inc/search.php');
require_once(DOKU_INC.'inc/pageutils.php');
require_once(DOKU_INC.'inc/JSON.php');
require_once(DOKU_COMMAND.'abstract_rest_command_class.php');

/**
 * Description of ns_tree_rest_command
 *
 * @author Josep Cañellas
 */
class ns_tree_rest_command extends abstract_rest_command_class{
    
    public function __construct() {
        parent::__construct();
        $this->defaultContentType="application/json";
        $this->supportedContentTypes=array("application/json");
        $this->supportedMethods=array("GET");
        $this->types['currentnode'] = abstract_command_class::T_OBJECT;
        $this->types['sortBy'] = abstract_command_class::T_INTEGER;
        $this->types['onlyDirs'] = abstract_command_class::T_BOOLEAN;
        //$this->types['explore'] = abstract_command_class::T_INTEGER;
        //$this->types['open'] = abstract_command_class::T_INTEGER;
        //$this->types['refreshAll'] = abstract_command_class::T_BOOLEAN;

        $defaultValues = array(
                            'sortBy' => 0
                          , 'onlyDirs' => FALSE
                );

        $this->setParameters($defaultValues);
    }

    public function processGet($extra_url_params) {
//        global $conf;
//        $sortOptions=array(0 => 'name', 'date');
//        $tree = array();
//        $tree_json=  array();
        $strData;
        $json = new JSON();
        
        if(!is_null($extra_url_params)){
            $this->setParamValuesFromUrl($extra_url_params);
        }
        
        $tree = $this->modelWrapper->getNsTree($this->params['currentnode'] 
                                                    , $this->params['sortBy']
                                                    , $this->params['onlyDirs']);
        
        $strData = $json->enc($tree);
        return $strData;      
        
    }
    
//    protected function startCommand(){        
//    }
//    
//    protected function preprocess(){        
//    }

    private function setParamValuesFromUrl($extra_url_params){
            $this->setCurrentNode($extra_url_params);
            $this->setOnlyDirs($extra_url_params);
            $this->setSortBy($extra_url_params);        
    }


    private function setCurrentNode($extra_url_params){
        $id = count($extra_url_params)-1;
        $this->params['currentnode'] = $extra_url_params[$id];
    }

    private function setSortBy($extra_url_params){
        if(count($extra_url_params)>3){ 
            $this->params['sortBy'] = $extra_url_params[2];
        }
    }

    private function setOnlyDirs($extra_url_params){
        if(count($extra_url_params)>2){ 
            $ret = ($extra_url_params[1]!='f' 
                            && $extra_url_params[1]!='false');
            $this->params['onlyDirs'] = $ret;
        }
        
    }
}

?>
