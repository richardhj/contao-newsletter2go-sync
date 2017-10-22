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
 * Back end modules
 */
array_insert(
    $GLOBALS['BE_MOD']['system'],
    4,
    [
        'newsletter2go_users' => [
            'tables'       => [Richardhj\Newsletter2Go\Contao\Model\Newsletter2GoUser::getTable()],
            'icon'         => 'system/modules/newsletter/assets/icon.gif',
            'authenticate' => ['Newsletter2Go\ContaoSync\Dca\Newsletter2GoUser', 'authenticateUser'],
        ],
    ]
);


/**
 * Models
 */
$GLOBALS['TL_MODELS'][Richardhj\Newsletter2Go\Contao\Model\Newsletter2GoUser::getTable()] =
    'Newsletter2Go\ContaoSync\Model\Newsletter2GoUser';


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['activateRecipient'][] = ['Newsletter2Go\ContaoSync\Hooks', 'activateRecipient'];
$GLOBALS['TL_HOOKS']['removeRecipient'][]   = ['Newsletter2Go\ContaoSync\Hooks', 'removeRecipient'];
