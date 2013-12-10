<?php

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
if(!defined('DOKU_COMMAND')) define('DOKU_COMMAND',DOKU_PLUGIN."ajaxcommand/");
require_once(DOKU_INC.'inc/search.php');
require_once(DOKU_INC.'inc/pageutils.php');
require_once(DOKU_INC.'inc/JSON.php');
require_once(DOKU_COMMAND.'abstract_command_class.php');

/**
 * Description of ns_tree_command
 *
 * @author professor
 */
class ns_tree_command extends abstract_command_class {
    /*PARAMETRES:
     * currentns: object (default=null)
     * sortBy: integer (default=0) (opcions={0=>name, 1=>date}
     * explore: integer (default=1)
     * open: integer (default=1)
     * refreshAll: boolean (default=false)
     */
    public function __construct() {
        $this->types['currentnode'] = abstract_command_class::T_OBJECT;
        $this->types['sortBy'] = abstract_command_class::T_INTEGER;
        $this->types['explore'] = abstract_command_class::T_INTEGER;
        $this->types['open'] = abstract_command_class::T_INTEGER;
        $this->types['refreshAll'] = abstract_command_class::T_BOOLEAN;

        $defaultValues = array(
                            'sortBy' => 0,
                            'explore' => 1,
                            'open' => 1,
                            'refreshAll' => FALSE
                );
        
        $this->setParameters($defaultValues);
    }


    //put your code here
    protected function _run() {
        $sortOptions=array(0 => 'name', 'date');
        global $conf;
        $tree = array();
        $strData;
        $json = new JSON();
        
        if($this->params['currentnode']){
            $node = $this->params['currentnode'];
        }else{
            $node = array('id' =>'', 'level' => 0, 'open' => true, 'type' => 'd');
        }
        $sort=$sortOptions[$this->params['sortBy']];
        $base=$conf['datadir'];
        
        $maxLevel = $node['level']+$this->params['explore'];
        
        $tree = $this->generateFileTree($node, $maxLevel, $base, $sort );
        
        $strData = $json->enc($tree);
        return $strData;
    }
    
//    protected function generateFileTree($root, $maxLevel, $base, $sort=false){
//        
//        search(&$data,$base,$func,$opts,$dir='',$lvl=1,$sort=false);
//    }
    
    protected function generateFileTree($root, $maxLevel, $base, $sort=false){
        $nodeQueue = array();
//        array_push($nodeQueue, $root);
        $nodeQueue[]= & $root;
//        $currentLevel = $root['level'];
        while(count($nodeQueue)>0){
//            $node = array_pop($nodeQueue);
            if(isset($node)){
                unset($node);
            }
            reset($nodeQueue);
            $key = key($nodeQueue);
            $node = & $nodeQueue[$key];
            unset($nodeQueue[$key]);
            
            $data = array();
            $opts = array('ns' => $node['id']);
            $dir = str_replace(':', '/', $node['id']);
            search($data, $base, 'ns_tree_command::setInfo', 
                    $opts, $dir, $node['level']+1, $sort);
            if($node['type']==='d' && count($data)>0){
                $node['children']=array();
            }
            foreach ($data as $value) {
                $node['children'][]=& $value;                
                $currentLevel=$value['level'];
                if($this->params['open']<$currentLevel){
                    $value['open']=false;
                }
                if($value['type']=='d' && $currentLevel<$maxLevel){
//                    array_push($nodeQueue, $value);
                    $nodeQueue[]= & $value;
                }
                unset($value);
            }
        }
        return $root;
    }

    public function setInfo(&$data,$base,$file,$type,$lvl,$opts){
        search_index($data, $base, $file, $type, $lvl, $opts);
    }

    protected function getDokuwikiAct() {
        return "";
    }    
}

?>
