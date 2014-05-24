<?php
/**
 * Default settings for the ajaxcommand plugin
 *
 * @author Josep Cañellas <jcanell4@ioc.cat> 
 */

$conf['debugLvl']                 = 1;                  // debug mode level -- more verbose ( 0: no display; 1: display error msg; 3: display&log error msg all msg; 3: display&log all )
$conf['logFile']                  = '';                 // log File where $this->_msg write with debugLvl >= 2
$conf['processingImageRepository'] = '/repository/pde/';  // repository for generated images using pde algorithms
$conf['processingXmlFile'] = 'lib/_java/pde/xml/algorismes.xml';  // registry of pde algorithms.
