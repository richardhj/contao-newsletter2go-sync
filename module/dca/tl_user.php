<?php


/** @noinspection PhpUndefinedMethodInspection */
$table = \UserModel::getTable();

/**
 * Palettes
 */
$GLOBALS['TL_DCA'][$table]['palettes']['__selector__'][] = 'n2g_active';
foreach ($GLOBALS['TL_DCA'][$table]['palettes'] as $name=>$palette) {
    if ('__selector__' === $name) {
        continue;
    }

$GLOBALS['TL_DCA'][$table]['palettes'][$name] .= ';{newsletter2go_legend},n2g_active';
}


/**
 * Subpalettes
 */
$GLOBALS['TL_DCA'][$table]['subpalettes']['n2g_active'] = 'n2g_user';


/**
 * Fields
 */
$GLOBALS['TL_DCA'][$table]['fields']['n2g_active'] = [
    'label'     => &$GLOBALS['TL_LANG'][$table]['n2g_active'],
    'inputType' => 'checkbox',
    'eval'      => [
        'submitOnChange' => true,
        'tl_class'       => 'w50 m12',
    ],
    'sql' => "char(1) NOT NULL default ''"
];

$GLOBALS['TL_DCA'][$table]['fields']['n2g_user'] = [
    'label'     => &$GLOBALS['TL_LANG'][$table]['n2g_user'],
    'inputType' => 'select',
    'foreignKey' => Newsletter2Go\ContaoSync\Model\Newsletter2GoUser::getTable().'.name',
    'eval'      => [
        'mandatory' => true,
        'tl_class'  => 'w50',
    ],
    'sql' => "int(10) unsigned NOT NULL default '0'"
];