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

use Richardhj\Newsletter2Go\Contao\SyncBundle\Dca\NewsletterChannel;


/**
 * Config
 */
//$GLOBALS['TL_DCA']['tl_newsletter_channel']['config']['onload_callback'][] = ['CleverreachSync\Helper\Dca', 'syncNewsletterChannelsWithGroups'];
//@todo add ondelete_callback
//@todo add onsubmit_callback


/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_newsletter_channel']['palettes']['default'] .= ';{newsletter2go_legend},n2g_group_id';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_newsletter_channel']['fields']['n2g_group_id'] = [
    'label'            => &$GLOBALS['TL_LANG']['tl_newsletter_channel']['n2g_group_id'],
    'exclude'          => true,
    'inputType'        => 'select',
    'options_callback' => [NewsletterChannel::class, 'getNewsletter2GoGroups'],
    'eval'             => [
        'unique' => true
    ],
    'sql'              => "varchar(8) NOT NULL default ''"
];
