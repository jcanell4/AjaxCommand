<?php
/**
 * Add link to call a command
 *
 * @license    GNU_GPL_v2
 * @author     Josep Cañellas <jcanell4@ioc.cat>
 */
if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

class syntax_plugin_ajaxcommand_fetch extends DokuWiki_Syntax_Plugin {

    function getInfo(){
        return confToHash(dirname(__FILE__).'/plugin.info.txt');
    }

    function getType(){ return 'formatting'; }
    function getPType(){ return 'normal'; }
    function getSort(){ return 100; }

    function connectTo($mode) {
        $this->Lexer->addSpecialPattern("<<__fetch__<(?:(?:[^[\]]*?\[.*?\])|.*?)>>>",$mode , 'plugin_ajaxcommand_fetch');
    }

    function handle($match, $state, $pos, &$handler){
        list($call, $msg)= array_pad(explode('|', preg_replace(array('/^<<__fetch__</','/>>>$/u'),'',$match)), 2, "");
        return array($call, $msg);
    }

    function render($mode, &$renderer, $indata) {

        if($mode == 'xhtml'){
          list($mediaId, $msg) = $indata;
          $path = "lib/exe/fetch.php";
          $renderer->doc .= '<a href ="'.DOKU_BASE.$path.'?media='.$mediaId.'" class="wikilink1 nocommand">'.$msg.'</a>';
          return true;
        } elseif($mode == 'iocexportl' or $mode == 'iocxhtml' or $mode == 'ioccounter'){
          return true;
        }
        // unsupported $mode
        return false;
    }
}
