<?php
/**
 * Default settings for the ajaxcommand plugin
 *
 * @author Josep Cañellas <jcanell4@ioc.cat> 
 */

$conf['debugLvl']                       = 1;             // debug mode level -- more verbose ( 0: no display; 1: display error msg; 3: display&log error msg all msg; 3: display&log all )
$conf['logFile']                        = '';                 // log File where $this->_msg write with debugLvl >= 2
$conf['processingImageRepository']      = 'repository/pde/';  // repository for generated images using pde algorithms
$conf['processingXmlFile']              = 'lib/_java/pde/xml/algorismes.xml';  // registry of pde algorithms.
$conf['processingPdeRepository']        = 'lib/_java/pde/algorismes/'; //repository for pde algorithms
$conf['processingClassesRepository']    = 'lib/_java/pde/classes/'; //repository for pde classes
$conf['processingSrcRepository']        = 'lib/_java/pde/src/'; //repository for pde src
$conf['processingPackage']              = 'ioc/wiki/processingmanager/'; //processing package
$conf['javaLibDir']                     = 'lib/_java/lib/'; //java libraries
$conf['javaDir']                        = 'lib/_java/'; //java libraries
$conf['javaLibs']                       = 'ImageGenerator.jar,'
                                            . 'core.jar,'
                                            . 'gluegen-rt-natives-linux-i586.jar,'
                                            . 'gluegen-rt.jar,'
                                            . 'itext.jar,'
                                            . 'jogl-all-natives-linux-i586.jar,'
                                            . 'jogl-all.jar,'
                                            . 'pdf.jar,'
                                            . 'commons-codec-1.9.jar,'
                                            . 'javax.json-1.0.2.jar'; //java libraries

//$conf['paramModelManagerType'] = 'projectType';

// Avisos del sistema
$conf['system_warning_user'] = 'Avís del sistema';
$conf['system_warning_title'] = '';
$conf['system_warning_message'] = '';
$conf['system_warning_start_date'] = '31-12-2000 00:00';
$conf['system_warning_end_date'] = '31-12-2000 00:00';
$conf['system_warning_type'] = 'warning';