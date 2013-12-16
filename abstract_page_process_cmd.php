<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of abstract_page_process_cmd
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
require_once(dirname(__FILE__).'/abstract_command_class.php');
//require_once 'HTTP.php';

abstract class abstract_page_process_cmd extends abstract_command_class {
    
    protected function startCommand(){
        $this->modelWrapper->startPageProcess(
                $this->getDwAct(),
                $this->getDwId(),
                $this->getDwRev(),
                $this->getDwRange(),
                $this->getDwDate(),
                $this->getDwPre(),
                $this->getDwText(),
                $this->getDwSuf(),
                $this->getDwSum());
    }    
}

?>
