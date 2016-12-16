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

// Avisos del sistema
$meta['system_warning_user'] = ['string'];
$meta['system_warning_title'] = ['string'];
$meta['system_warning_message'] = ['string'];
$meta['system_warning_show_alert'] = ['onoff'];
$meta['system_warning_start_date'] = ['string', '_pattern' => '/\d\d-\d\d-\d\d\d\d \d\d:\d\d/'];
$meta['system_warning_end_date'] = ['string', '_pattern' => '/\d\d-\d\d-\d\d\d\d \d\d:\d\d/'];
$meta['system_warning_type'] = ['multichoice', '_choices' => ['error', 'warning', 'info', 'success']];
