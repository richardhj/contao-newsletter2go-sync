<?php
/**
 * Newsletter2Go Synchronization for Contao Open Source CMS
 *
 * Copyright (c) 2015-2017 Richard Henkenjohann
 *
 * @package Newsletter2GoSync
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */


$table = Newsletter2Go\ContaoSync\Model\Newsletter2GoUser::getTable();


/**
 * Legends
 */
$GLOBALS['TL_LANG'][$table]['title_legend'] = 'Name und Key';


/**
 * Fields
 */
$GLOBALS['TL_LANG'][$table]['name'][0]    = 'Name';
$GLOBALS['TL_LANG'][$table]['name'][1]    = 'Bitte geben Sie einen internen Namen für den API-Benutzer ein.';
$GLOBALS['TL_LANG'][$table]['authKey'][0] = 'Auth-Key';
$GLOBALS['TL_LANG'][$table]['authKey'][1] = 'Bitte geben Sie den Auth-Key ein, den Sie im Backend von Newsletter2Go finden.';

/**
 * Actions
 */
$GLOBALS['TL_LANG'][$table]['new'][0]          = 'Neuer API-Benutzer';
$GLOBALS['TL_LANG'][$table]['new'][1]          = 'Einen neuen API-Benutzer erstellen';
$GLOBALS['TL_LANG'][$table]['edit'][0]         = 'Bearbeiten';
$GLOBALS['TL_LANG'][$table]['edit'][1]         = 'Den API-Benutzer ID %s bearbeiten';
$GLOBALS['TL_LANG'][$table]['show'][0]         = 'Details zeigen';
$GLOBALS['TL_LANG'][$table]['show'][1]         = 'Details vom API-Benutzer ID %s anzeigen';
$GLOBALS['TL_LANG'][$table]['authenticate'][0] = 'Authentifizieren';
$GLOBALS['TL_LANG'][$table]['authenticate'][1] = 'Den API-Benutzer ID %s authentifizieren';
