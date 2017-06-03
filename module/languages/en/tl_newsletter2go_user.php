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
$GLOBALS['TL_LANG'][$table]['title_legend'] = 'Name and key';


/**
 * Fields
 */
$GLOBALS['TL_LANG'][$table]['name'][0]    = 'Name';
$GLOBALS['TL_LANG'][$table]['name'][1]    = 'Please enter an internal name for this user.';
$GLOBALS['TL_LANG'][$table]['authKey'][0] = 'Auth key';
$GLOBALS['TL_LANG'][$table]['authKey'][1] = 'Please enter the API auth key which can be found in the Newsletter2Go system.';


/**
 * Actions
 */
$GLOBALS['TL_LANG'][$table]['new'][0]          = 'New API user';
$GLOBALS['TL_LANG'][$table]['new'][1]          = 'Create a new API user';
$GLOBALS['TL_LANG'][$table]['edit'][0]         = 'Edit';
$GLOBALS['TL_LANG'][$table]['edit'][1]         = 'Edit the API user ID %s';
$GLOBALS['TL_LANG'][$table]['delete'][0]       = 'Delete';
$GLOBALS['TL_LANG'][$table]['delete'][1]       = 'Delete the API user ID %s';
$GLOBALS['TL_LANG'][$table]['show'][0]         = 'Show details';
$GLOBALS['TL_LANG'][$table]['show'][1]         = 'Show details of API user ID %s';
$GLOBALS['TL_LANG'][$table]['authenticate'][0] = 'Authenticate';
$GLOBALS['TL_LANG'][$table]['authenticate'][1] = 'Authenticate API user ID %s';


/**
 * Authentication back end
 */
$GLOBALS['TL_LANG'][$table]['be_user_auth']['headline']                    = 'Authenticate the API user';
$GLOBALS['TL_LANG'][$table]['be_user_auth']['authentication_confirmation'] = 'You are logged in as: %s';
$GLOBALS['TL_LANG'][$table]['be_user_auth']['submit']                      = 'Authenticate';
$GLOBALS['TL_LANG'][$table]['be_user_auth']['tip']                         = 'Your credentials (username/password) will not be saved.';
