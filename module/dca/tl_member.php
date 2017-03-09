<?php
/**
 * Newsletter2Go Synchronization for Contao Open Source CMS
 *
 * Copyright (c) 2015-2017 Richard Henkenjohann
 *
 * @package Newsletter2GoSync
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */


/**
 * Config
 */
$GLOBALS['TL_DCA']['tl_member']['config']['ondelete_callback'][] = ['Newsletter2Go\ContaoSync\Helper\Dca', 'deleteMember'];


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_member']['fields']['groups']['save_callback'][] = ['Newsletter2Go\ContaoSync\Helper\Dca', 'syncMemberGroupsWithNewsletter2Go'];
//$GLOBALS['TL_DCA']['tl_member']['fields']['cr_receiver_id'] = [
//	'sql' => "int(10) NOT NULL default '0'"
//	];
