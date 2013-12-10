<?php
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
if(!defined('CURRENT_NODE_NS_TREE_PARAM')) define('CURRENT_NODE_NS_TREE_PARAM', 1);
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
        //$this->types['explore'] = abstract_command_class::T_INTEGER;
        //$this->types['open'] = abstract_command_class::T_INTEGER;
        //$this->types['refreshAll'] = abstract_command_class::T_BOOLEAN;

        $defaultValues = array(
                            'sortBy' => 0
                );

        $this->setParameters($defaultValues);
    }

    public function processGet($extra_url_params) {
        global $conf;
        $sortOptions=array(0 => 'name', 'date');
        $tree = array();
        $tree_json=  array();
        $strData;
        $json = new JSON();
        
        if(!is_null($extra_url_params)){
            $this->params['currentnode'] = $extra_url_params[CURRENT_NODE_NS_TREE_PARAM];
        }
        
        
        if($this->params['currentnode']=="_"){
            return $json->enc(array('id' => "", 'name' => "", 'type' => 'd'));
            
        }
        if($this->params['currentnode']){
            $node = $this->params['currentnode'];
            $aname = split(":", $this->params['currentnode']);
            $level = count($aname);
            $name = $aname[$level-1];
        }else{
            $node = '';
            $name = '';
            $level=0;
        }
        $sort=$sortOptions[$this->params['sortBy']];
        $base=$conf['datadir'];
        
        $opts = array('ns' => $node);
        $dir = str_replace(':', '/', $node);
        search($tree, $base, 'search_index', 
                    $opts, $dir, 1);
        foreach(array_keys($tree) as $item){
            $tree_json[$item]['id'] = $tree[$item]['id'] ;
            $aname = split(":", $tree[$item]['id']);
            $tree_json[$item]['name'] = $aname[$level];
            $tree_json[$item]['type'] = $tree[$item]['type'];
        }
        
        $strData = $json->enc(array('id' => $node, 'name' => $node, 
                                'type' => 'd', 'children' => $tree_json));
//        $strData = $json->enc($tree);
        return $strData;      
        
    }

    protected function getDokuwikiAct() {
        return "";
    }
}

?>
