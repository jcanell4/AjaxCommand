<?php
/**
 * @author josep
 * @modified by Rafael Claver
 */
if (!defined('DOKU_INC')) die();
if (!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN', DOKU_INC . "lib/plugins/");
include_once (DOKU_INC . "lib/lib_ioc/wikiiocmodel/ResourceLockerInterface.php");
include_once (DOKU_PLUGIN . "ajaxcommand/defkeys/ResponseHandlerKeys.php");

class LockKeys extends ResponseHandlerKeys {
    const LOCKED        = ResourceLockerInterface::LOCKED;  //Si es fa una consulta: indica que el recurs es pot bloquejar
                                                            //Si es fa una petició de bloqueix: indica que el recurs s'ha bloquejat amb éxit. A més, s'enregistra
                                                            //el bloqueig en el registre estès de bloquejos (Extended Lock File).
                                                            //L'extended lock file és un fitxer informatiu on trobarem la informació de l'usuari que estigui bloquejant un recurs i també d'aquells usuaris que expressin el seu desig de bloquejar-lo. Cada recurs disposarà d'un fitxer diferent.
    const REQUIRED      = ResourceLockerInterface::REQUIRED;    //Indica que el recurs estava ja bloquejat per un altre usuari i no s'ha pogut bloquejar, però que s'ha posta la petició de bloqueig a la cua de peticions, expressant el desig de bloqueig, al fitxer Extended Lock File.
    const LOCKED_BEFORE = ResourceLockerInterface::LOCKED_BEFORE;   //Indica que l'usuari ja ha bloquejat prèviament el mateix recurs, probablement des d'un altre ordinador.
}
