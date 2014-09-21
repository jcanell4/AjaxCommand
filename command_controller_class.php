<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of command_controller_class
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */

require_once (DOKU_INC.'inc/plugincontroller.class.php');

class command_controller_class {
    //put your code here
    private $commandList;
    private $commandResponseList;
    
    public function getCommandClassName($command, $ennabled=TRUE){
        return $this->getCommandList(!$ennabled)[$command];
    }

    public function getResponseClassName($command, $ennabled=TRUE){
        return $this->getResponseList(!$ennabled)[$command];
    }
    
    public function getCommandList($all=FALSE){
        if(!isset($this->commandList['enabled'])){
            $this->commandList['enabled']=$this->_getCommandList(TRUE);
        }
        
        if(!isset($this->commandList['disabled'])){
            $this->commandList['disabled']=$this->_getCommandList(FALSE);
        }

        return $all ? array_merge($this->commandList['enabled'],
                                    $this->commandList['disabled']) : 
                                            $this->commandList['enabled'];
    }    
    
    public function getResponseList($all=FALSE){
        if(!isset($this->$commandResponseList['enabled'])){
            $this->$commandResponseList['enabled']=$this->_getResponseList(TRUE);
        }
        
        if(!isset($this->$commandResponseList['disabled'])){
            $this->$commandResponseList['disabled']=$this->_getResponseList(FALSE);
        }

        return $all ? array_merge($this->$commandResponseList['enabled'],
                                    $this->$commandResponseList['disabled']) : 
                                          $this->$commandResponseList['enabled'];
    }
    
    private function _getCommandList($enabled) {
        
    }
    
    private  function _getResponseList($enabled) {
//        global $plugin_controller;  
//        $master_list = $plugin_controller->getList('', $enabled);
//
//        $plugins = array();
//        foreach ($master_list as $plugin) {
//            $dir = $this->get_directory($plugin);
//
//            if (@file_exists(DOKU_PLUGIN."$dir/$type.php")){
//                $plugins[] = $plugin;
//            } else {
//                if ($dp = @opendir(DOKU_PLUGIN."$dir/$type/")) {
//                    while (false !== ($component = readdir($dp))) {
//                        if (substr($component,0,1) == '.' || strtolower(substr($component, -4)) != ".php") continue;
//                        if (is_file(DOKU_PLUGIN."$dir/$type/$component")) {
//                            $plugins[] = $plugin.'_'.substr($component, 0, -4);
//                        }
//                    }
//                    closedir($dp);
//                }
//            }
//        }
//
//        return $plugins;
    }
}

?>
