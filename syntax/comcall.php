<?php
/**
 * Add link to call a command
 *
 * @license    GNU_GPL_v2
 * @author     Josep Cañellas <jcanell4@ioc.cat>
 */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_INC')) define('DOKU_INC',realpath(dirname(__FILE__).'/../../').'/');
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN . 'ownInit/WikiGlobalConfig.php');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_ajaxcommand_comcall extends DokuWiki_Syntax_Plugin {

    function getInfo(){
        return confToHash(dirname(__FILE__).'/plugin.info.txt');
    }

    function getType(){ return 'formatting'; }
    function getPType(){ return 'normal'; }
    function getSort(){ return 100; }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern("<<__cc__<(?:(?:[^[\]]*?\[.*?\])|.*?)>>>",$mode , 'plugin_ajaxcommand_comcall');
    }

    function handle($match, $state, $pos, &$handler){
        list($call, $msg)= array_pad(explode('|', preg_replace(array('/^<<__cc__</','/>>>$/u'),'',$match)), 2, "");
        return array($call, $msg);
    }

    function render($mode, &$renderer, $indata) {

        if($mode == 'xhtml'){
            list($call, $msg) = $indata;
            //$pos = strlen(DOKU_INC);
            //$path = substr(__DIR__, $pos)."/../ajax.php";
            $path = "lib/exe/ioc_ajax.php";
            $renderer->doc .= '<a href ="'.DOKU_BASE.$path.'?'.$call.'" class="wikilink1">'.$msg.'</a>';
            return true;
        } elseif($mode == 'iocexportl' or $mode == 'iocxhtml' or $mode == 'ioccounter'){
            return true;
        }
        // unsupported $mode
        return false;
    }
}
