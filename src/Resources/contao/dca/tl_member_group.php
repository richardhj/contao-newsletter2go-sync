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

use Richardhj\Newsletter2Go\Contao\SyncBundle\Dca\MemberGroup;


/**
 * Config
 */
//@todo $GLOBALS['TL_DCA']['tl_member_group']['config']['onload_callback'][] = array('CleverreachSync\Helper\Hooks', 'syncNewsletterChannelsWithGroups');
$GLOBALS['TL_DCA']['tl_member_group']['config']['onsubmit_callback'][] =
    [MemberGroup::class, 'createNewsletter2GoGroup'];
$GLOBALS['TL_DCA']['tl_member_group']['config']['ondelete_callback'][] =
    [MemberGroup::class, 'deleteMemberGroup'];


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_member_group']['palettes']['__selector__'][] = 'n2g_sync';
$GLOBALS['TL_DCA']['tl_member_group']['palettes']['default']        .= ';{newsletter2go_legend},n2g_sync';


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_member_group']['subpalettes']['n2g_sync'] = 'n2g_group_id';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_member_group']['fields']['n2g_sync'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_member_group']['n2g_sync'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'w50 m12',
    ],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_member_group']['fields']['n2g_group_id'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_member_group']['n2g_group_id'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => [MemberGroup::class, 'getNewsletter2GoGroups'],
    'eval'             => [
        'unique'             => true,
        'includeBlankOption' => true,
        'blankOptionLabel'   => '(neu erstellen)', //@todo lang
        'tl_class'           => 'w50',
    ],
    'sql'              => "varchar(8) NOT NULL default ''",
];
