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
 * Config
 */
$GLOBALS['TL_DCA']['tl_member']['config']['ondelete_callback'][] = ['Newsletter2Go\ContaoSync\Dca\Member', 'deleteMember'];


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_member']['fields']['groups']['save_callback'][] = ['Newsletter2Go\ContaoSync\Dca\Member', 'syncMemberGroupsWithNewsletter2Go'];

$GLOBALS['TL_DCA']['tl_member']['fields']['n2g_receiver_id'] = [
    'sql' => "varchar(8) NOT NULL default ''",
];
