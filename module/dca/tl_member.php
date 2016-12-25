<?php


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
