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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] .= ';{n2g_legend},n2g_sync_fields,n2g_default_user';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_settings']['fields']['n2g_sync_fields'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_settings']['n2g_sync_fields'],
    'inputType'        => 'select',
    'options_callback' => function () {
        $defaultAttributes = ['email', 'phone', 'gender', 'firstname', 'lastname', 'birthday'];
        $dcaLoader         = new \Contao\DcaLoader('tl_member');
        $dcaLoader->load();
        $customAttributes = array_keys($GLOBALS['TL_DCA']['tl_member']['fields']);
        $customAttributes = array_diff($customAttributes, $defaultAttributes);

        return ['default' => $defaultAttributes, 'custom' => $customAttributes];
    },
    'reference'        => &$GLOBALS['TL_LANG']['tl_member'],
    'eval'             => [
        'tl_class' => 'w50',
        'multiple' => true,
        'chosen'   => true,
    ],
    'sql'              => 'text NULL',
];

$GLOBALS['TL_DCA']['tl_settings']['fields']['n2g_default_user'] = [
    'label'      => &$GLOBALS['TL_LANG']['tl_settings']['n2g_default_user'],
    'inputType'  => 'select',
    'foreignKey' => 'tl_newsletter2go_user.name',
    'eval'       => [
        'includeBlankOption' => true,
        'tl_class'           => 'w50',
    ],
    'sql'        => "int(10) unsigned NOT NULL default '0'",
];
