<?php
/**
 * Newsletter2Go Synchronization for Contao Open Source CMS
 *
 * Copyright (c) 2015-2017 Richard Henkenjohann
 *
 * @package Newsletter2GoSync
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */

namespace Newsletter2Go\ContaoSync\Dca;


use Newsletter2Go\Api\Model\NewsletterGroup;
use Newsletter2Go\Api\Model\NewsletterRecipient;
use Newsletter2Go\ContaoSync\AbstractHelper;

class MemberGroup extends AbstractHelper
{

    /**
     * Create a group on Newsletter2Go if sync for member group was enabled but no existing group was selected
     * @category onsubmit_callback (table: tl_member_group)
     *
     * @param \DataContainer $dc
     */
    public function createNewsletter2GoGroup(\DataContainer $dc)
    {
        if (!$dc->id) {
            return;
        }

        /** @var \MemberGroupModel|\Model $memberGroup */
        $memberGroup = \MemberGroupModel::findByPk($dc->id);

        if ($memberGroup->n2g_sync || !$memberGroup->n2g_group_id) {
            $group = new NewsletterGroup();
            $group->setApiCredentials(self::getApiCredentials());
            $group->setListId(self::getListId());
            $group->setName($memberGroup->name);
            $group->save();

            $memberGroup->n2g_group_id = $group->getId();
            $memberGroup->save();
        }
    }


    /**
     * Remove a group's members from the Newsletter2Go group after deleting a member group
     * @category ondelete_callback (table: tl_member_group)
     *
     * @param $dc
     */
    public function deleteMemberGroup(\DataContainer $dc)
    {
        if (!$dc->id || !$dc->activeRecord->n2g_sync || !$dc->activeRecord->n2g_group_id) {
            return;
        }

        $members = \Database::getInstance()
            ->prepare(
                'SELECT m.email FROM tl_member AS m INNER JOIN tl_member_to_group mg ON m.id=mg.member_id WHERE mg.group_id=?'
            )
            ->execute($dc->id)
            ->fetchEach('email');

        foreach ($members as $member) {
            $recipient = new NewsletterRecipient();
            $recipient->setApiCredentials(self::getApiCredentials());
            $recipient->setListId(self::getListId());
            $recipient->setEmail($member);
            // Saving a recipient will fetch the id
            $recipient->save();

            $recipient->removeFromGroup($dc->activeRecord->n2g_group_id);
        }
    }
}
