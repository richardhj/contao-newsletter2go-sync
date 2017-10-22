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

namespace Richardhj\Newsletter2Go\Contao\Dca;

use Contao\Database;
use Contao\DataContainer;
use Contao\MemberGroupModel;
use Richardhj\Newsletter2Go\Api\Model\NewsletterGroup;
use Richardhj\Newsletter2Go\Api\Model\NewsletterRecipient;
use Richardhj\Newsletter2Go\Contao\AbstractHelper;


/**
 * Class MemberGroup
 *
 * @package Richardhj\Newsletter2Go\Contao\Dca
 */
class MemberGroup extends AbstractHelper
{

    /**
     * Create a group on Newsletter2Go if sync for member group was enabled but no existing group was selected
     *
     * @category onsubmit_callback (table: tl_member_group)
     *
     * @param DataContainer $dc
     */
    public function createNewsletter2GoGroup(DataContainer $dc)
    {
        if (!$dc->id) {
            return;
        }

        // Check if user has N2G sync enabled
        if (null === ($apiCredentials = self::getApiCredentials())) {
            return;
        }

        /** @var MemberGroupModel|\Model $memberGroup */
        $memberGroup = MemberGroupModel::findByPk($dc->id);

        if ($memberGroup->n2g_sync && !$memberGroup->n2g_group_id) {
            $group = new NewsletterGroup();
            $group->setApiCredentials($apiCredentials);
            $group->setListId(self::getListId());
            $group->setName($memberGroup->name);
            $group->save();

            $memberGroup->n2g_group_id = $group->getId();
            $memberGroup->save();
        }
    }

    /**
     * Remove a group's members from the Newsletter2Go group after deleting a member group
     *
     * @category ondelete_callback (table: tl_member_group)
     *
     * @param $dc
     */
    public function deleteMemberGroup(DataContainer $dc)
    {
        if (!$dc->id || !$dc->activeRecord->n2g_sync || !$dc->activeRecord->n2g_group_id) {
            return;
        }

        // Check if user has N2G sync enabled
        if (null === ($apiCredentials = self::getApiCredentials())) {
            return;
        }

        $members = Database::getInstance()
            ->prepare(
                'SELECT m.email FROM tl_member AS m INNER JOIN tl_member_to_group mg ON m.id=mg.member_id WHERE mg.group_id=?'
            )
            ->execute($dc->id)
            ->fetchEach('email');

        foreach ($members as $member) {
            $recipient = new NewsletterRecipient();
            $recipient->setApiCredentials($apiCredentials);
            $recipient->setListId(self::getListId());
            $recipient->setEmail($member);
            // Saving a recipient will fetch the id
            $recipient->save();

            $recipient->removeFromGroup($dc->activeRecord->n2g_group_id);
        }
    }
}
