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
 * DCA config
 */
$GLOBALS['TL_DCA'][$table] = [

    // Config
    'config'   => [
        'dataContainer' => 'Table',
        'sql'           =>
            [
                'keys' => [
                    'id' => 'primary',
                ],
            ],
    ],

    // List
    'list'     => [
        'sorting'           => [
            'mode'        => 1,
            'fields'      => ['name'],
            'flag'        => 1,
            'panelLayout' => 'filter,search;limit',
        ],
        'label'             => [
            'fields' => ['name'],
            //            'group_callback'          => array('NotificationCenter\tl_epost_user', 'getGroupLabel')
        ],
        'global_operations' => [
            'all' => [
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations'        => [
            'edit'         => [
                'label' => &$GLOBALS['TL_LANG'][$table]['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ],
            'delete'       => [
                'label'      => &$GLOBALS['TL_LANG'][$table]['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm']
                                . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show'         => [
                'label' => &$GLOBALS['TL_LANG'][$table]['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ],
            'authenticate' => array
            (
                'label' => &$GLOBALS['TL_LANG'][$table]['authenticate'],
                'href'  => 'key=authenticate',
                'icon'  => 'assets/newsletter2go-sync/images/be-user-auth.png'
            )
        ],
    ],

    // Palettes
    'palettes' => [
        'default' => '{title_legend},name,authKey',
    ],

    // Fields
    'fields'   => [
        'id'               => [
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ],
        'tstamp'           => [
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ],
        'name'             => [
            'label'     => &$GLOBALS['TL_LANG'][$table]['name'],
            'exclude'   => true,
            'search'    => true,
            'inputType' => 'text',
            'eval'      => [
                'mandatory' => true,
                'maxlength' => 255,
                'tl_class'  => 'w50',
            ],
            'sql'       => "varchar(255) NOT NULL default ''",
        ],
        'authKey'          => [
            'label'     => &$GLOBALS['TL_LANG'][$table]['authKey'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'mandatory'    => true,
                'encrypt'      => true,
                //            'hideInput' => true,
                'preserveTags' => true,
                'tl_class'     => 'w50',
            ],
            'sql'       => "text NULL",
        ],
        'authRefreshToken' => [
            'label'     => &$GLOBALS['TL_LANG'][$table]['authRefreshToken'],
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => [
                'mandatory' => true,
                'encrypt'   => true,
                'hideInput' => true,
            ],
            'sql'       => "text NULL",
        ],
    ],
];
