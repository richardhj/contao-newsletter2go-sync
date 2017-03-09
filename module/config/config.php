<?php
/**
 * Newsletter2Go Synchronization for Contao Open Source CMS
 *
 * Copyright (c) 2015-2017 Richard Henkenjohann
 *
 * @package Newsletter2GoSync
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */


/**
 * Back end modules
 */
array_insert(
    $GLOBALS['BE_MOD']['system'],
    4,
    [
        'newsletter2go_users' => [
            'tables'       => [Newsletter2Go\ContaoSync\Model\Newsletter2GoUser::getTable()],
            'icon'         => 'system/modules/newsletter/assets/icon.gif',
            'authenticate' => ['Newsletter2Go\ContaoSync\Helper\Dca', 'authenticateUser'],
        ],
    ]
);


/**
 * Models
 */
$GLOBALS['TL_MODELS'][Newsletter2Go\ContaoSync\Model\Newsletter2GoUser::getTable(
)] = 'Newsletter2Go\ContaoSync\Model\Newsletter2GoUser';


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['activateRecipient'][] = ['Newsletter2Go\ContaoSync\Helper\Hooks', 'activateRecipient'];
$GLOBALS['TL_HOOKS']['removeRecipient'][] = ['Newsletter2Go\ContaoSync\Helper\Hooks', 'removeRecipient'];
