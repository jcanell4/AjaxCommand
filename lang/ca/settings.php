<?php
/**
 * English language setting file
 *
 * @author Greg BELLAMY <garlik.crx@gmail.com> [Gag]
 */

$lang['debugLvl']                 = "nivell de depuarció: 0-void, 1-Display error, 2-Log&Display error, 3-Log&Display all message";
$lang['logFile']                  = "ruta del fitxer de registre (log file)";
$lang['defCommandClassFolder']    = "ruta de la carpeta on es troben la classes de tipus comanda per defecte";
$lang['userCommandClassFolder']   = "ruta de la carpeta on es poden posar classes de tipus comanda definides per l'usuari";

// Avisos del sistema
$meta['system_warning_user'] = ['string'];
$meta['system_warning_title'] = ['string'];
$meta['system_warning_message'] = ['string'];
$meta['system_warning_show_alert'] = ['onoff'];
$meta['system_warning_start_date'] = ['string', '_pattern' => '/\d\d-\d\d-\d\d\d\d \d\d:\d\d/'];
$meta['system_warning_end_date'] = ['string', '_pattern' => '/\d\d-\d\d-\d\d\d\d \d\d:\d\d/'];
