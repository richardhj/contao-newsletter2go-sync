<?php

/**
 * This file is part of richardhj/contao-newsletter2go-sync.
 *
 * Copyright (c) 2016-2017 Richard Henkenjohann
 *
 * @package   richardhj/contao-newsletter2go-sync
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2016-2017 Richard Henkenjohann
 * @license   https://github.com/richardhj/richardhj/contao-newsletter2go-sync/blob/master/LICENSE LGPL-3.0
 */


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['title_legend'] = 'Name und Key';


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['name'][0]    = 'Name';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['name'][1]    = 'Bitte geben Sie einen internen Namen für den API-Benutzer ein.';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['authKey'][0] = 'Auth-Key';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['authKey'][1] = 'Bitte geben Sie den Auth-Key ein, den Sie im Backend von Newsletter2Go finden.';


/**
 * Actions
 */
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['new'][0]          = 'Neuer API-Benutzer';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['new'][1]          = 'Einen neuen API-Benutzer erstellen';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['edit'][0]         = 'Bearbeiten';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['edit'][1]         = 'Den API-Benutzer ID %s bearbeiten';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['delete'][0]       = 'Löschen';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['delete'][1]       = 'Den API-Benutzer ID %s löschen';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['show'][0]         = 'Details zeigen';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['show'][1]         = 'Details vom API-Benutzer ID %s anzeigen';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['authenticate'][0] = 'Authentifizieren';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['authenticate'][1] = 'Den API-Benutzer ID %s authentifizieren';


/**
 * Authentication back end
 */
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['be_user_auth']['headline']                    = 'Den API-Benutzer authentifizieren';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['be_user_auth']['authentication_confirmation'] = 'Sie sind angemeldet als: %s (%s)';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['be_user_auth']['submit']                      = 'Authentifizieren';
$GLOBALS['TL_LANG']['tl_newsletter2go_user']['be_user_auth']['tip']                         = 'Ihre Zugangsdaten (Benuztername/Passwort) werden nicht gespeichert';
