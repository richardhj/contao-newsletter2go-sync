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

use Richardhj\Newsletter2Go\Contao\SyncBundle\Dca\Newsletter2GoUser as Newsletter2GoUserDca;
use Richardhj\Newsletter2Go\Contao\SyncBundle\Hooks;
use Richardhj\Newsletter2Go\Contao\SyncBundle\Model\Newsletter2GoUser as Newsletter2GoUserModel;


/**
 * Back end modules
 */
array_insert(
    $GLOBALS['BE_MOD']['system'],
    4,
    [
        'newsletter2go_users' => [
            'tables'       => ['tl_newsletter2go_user'],
            'icon'         => 'system/modules/newsletter/assets/icon.gif',
            'authenticate' => [Newsletter2GoUserDca::class, 'authenticateUser'],
        ],
    ]
);


/**
 * Models
 */
$GLOBALS['TL_MODELS']['tl_newsletter2go_user'] = Newsletter2GoUserModel::class;


/**
 * Hooks
 */
$GLOBALS['TL_HOOKS']['activateRecipient'][] = [Hooks::class, 'activateRecipient'];
$GLOBALS['TL_HOOKS']['removeRecipient'][]   = [Hooks::class, 'removeRecipient'];
