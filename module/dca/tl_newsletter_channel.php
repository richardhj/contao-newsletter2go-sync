<?php



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
    'label' => &$GLOBALS['TL_LANG']['tl_newsletter_channel']['n2g_group_id'],
    'exclude' => true,
    'inputType' => 'select',
    'options_callback' => ['CleverreachSync\Helper\Hooks', 'getNewsletter2GoGroups'],
    'eval' => [
	    'unique'=>true
        ],
	'sql' => "varchar(8) NOT NULL default ''"
    ];
