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
$GLOBALS['TL_DCA']['tl_user']['palettes']['__selector__'][] = 'n2g_active';
foreach ((array)$GLOBALS['TL_DCA']['tl_user']['palettes'] as $name => $palette) {
    if (in_array($name, ['__selector__', 'login'], true)) {
        continue;
    }

    $GLOBALS['TL_DCA']['tl_user']['palettes'][$name] .= ';{newsletter2go_legend},n2g_active';
}


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA']['tl_user']['subpalettes']['n2g_active'] = 'n2g_user';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_user']['fields']['n2g_active'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['n2g_active'],
    'inputType' => 'checkbox',
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'w50 m12',
    ],
    'sql'       => "char(1) NOT NULL default ''",
];

$GLOBALS['TL_DCA']['tl_user']['fields']['n2g_user'] = [
    'label'      => &$GLOBALS['TL_LANG']['tl_user']['n2g_user'],
    'inputType'  => 'select',
    'foreignKey' => 'tl_newsletter2go_user.name',
    'eval'       => [
        'mandatory' => true,
        'tl_class'  => 'w50',
    ],
    'sql'        => "int(10) unsigned NOT NULL default '0'",
];
