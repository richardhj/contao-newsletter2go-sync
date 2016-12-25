<?php


/**
 * Config
 */
//@todo $GLOBALS['TL_DCA']['tl_member_group']['config']['onload_callback'][] = array('CleverreachSync\Helper\Hooks', 'syncNewsletterChannelsWithGroups');
$GLOBALS['TL_DCA']['tl_member_group']['config']['onsubmit_callback'][] = ['Newsletter2Go\ContaoSync\Helper\Dca', 'createN2GGroupForMemberGroup'];
$GLOBALS['TL_DCA']['tl_member_group']['config']['ondelete_callback'][] = [
    'Newsletter2Go\ContaoSync\Helper\Dca',
    'deleteMemberGroup',
];


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_member_group']['palettes']['__selector__'][] = 'n2g_sync';
$GLOBALS['TL_DCA']['tl_member_group']['palettes']['default'] .= ';{newsletter2go_legend},n2g_sync';


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
    'options_callback' => ['Newsletter2Go\ContaoSync\Helper\Dca', 'getNewsletter2GoGroups'],
    'eval'             => [
        'unique'   => true,
        'includeBlankOption' => true,
        'blankOptionLabel' => '(neu erstellen)', //@todo lang
        'tl_class' => 'w50',
    ],
    'sql'              => "varchar(8) NOT NULL default ''",
];
