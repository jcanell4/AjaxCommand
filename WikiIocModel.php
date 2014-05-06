<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
interface WikiIocModel {
    public function getHtmlPage($pid, $prev=NULL);  
    public function getCodePage($pdo, $pid, $prev=NULL, $prange=NULL);    
    public function cancelEdition($pid, $prev=NULL);
    public function saveEdition($pid, $prev=NULL, $prange=NULL, 
                $pdate=NULL, $ppre=NULL, $ptext=NULL, $psuf=null, $psum=NULL);  
    public function isDenied();
}

?>
