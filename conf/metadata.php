<?php
/**
 * Options for the odt2dw plugin
 *
 * @author Greg BELLAMY <garlik.crx@gmail.com> [Gag]
 */


$meta['debugLvl']                 = array('multichoice', '_choices' => array(0,1,2,3));
$meta['logFile']                  = array('string');
$meta['processingImageRepository'] = array('string');
$meta['processingXmlFile'] = array('string');
$meta['paramModelManagerType'] = array('string');