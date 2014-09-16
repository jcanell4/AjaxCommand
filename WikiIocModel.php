<?php

/**
 * Interface WikiIocModel
 *
 * @author Josep Cañellas <jcanell4@ioc.cat>
 */
interface WikiIocModel {
    public function getHtmlPage($pid, $prev = NULL);

    public function getCodePage($pdo, $pid, $prev = NULL, $prange = NULL);

    public function cancelEdition($pid, $prev = NULL);

    public function saveEdition($pid, $prev = NULL, $prange = NULL,
        $pdate = NULL, $ppre = NULL, $ptext = NULL, $psuf = NULL, $psum = NULL);

    public function isDenied();
}