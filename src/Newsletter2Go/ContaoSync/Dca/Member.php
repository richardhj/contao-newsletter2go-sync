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


use Newsletter2Go\Api\Model\NewsletterRecipient;
use Newsletter2Go\ContaoSync\AbstractHelper;

class Member extends AbstractHelper
{

    /**
     * Remove a member from all groups after deleting
     * @category ondelete_callback (table: tl_member)
     *
     * @param $dc
     */
    public function deleteMember(\DataContainer $dc)
    {
        if (!$dc->id) {
            return;
        }

        $recipient = new NewsletterRecipient();
        $recipient->setApiCredentials(self::getApiCredentials());
        $recipient->setListId(self::getListId());
        $recipient->setEmail($dc->activeRecord->email);

        // Fetch id
        $recipient->save();

        $groups = \Database::getInstance()
            ->prepare(
                'SELECT mg.n2g_group_id FROM tl_member_group AS mg INNER JOIN tl_member_to_group mtg ON mg.id=mtg.group_id WHERE mtg.member_id=? AND mg.n2g_sync=1'
            )
            ->execute($dc->id)
            ->fetchEach('n2g_group_id');

        foreach ($groups as $group) {
            $recipient->removeFromGroup($group);
        }
    }

    /**
     * Sync local member with group associated Newsletter2Go groups
     * @category save_callback (field: groups)
     *
     * @param mixed          $value The submitted groups as serialized string
     * @param \DataContainer $dc
     *
     * @return mixed
     */
    public function syncMemberGroupsWithNewsletter2Go($value, $dc)
    {
        $groups = deserialize($value);

        $groupsNew = $groups ?
            \Database::getInstance()
                ->query(
                    'SELECT n2g_group_id FROM tl_member_group WHERE id IN('.implode(',', $groups).') AND n2g_sync=1'
                )
                ->fetchEach('n2g_group_id')
            : [];

        $groupsOld = \Database::getInstance()
            ->prepare(
                'SELECT g.n2g_group_id FROM tl_member_to_group AS mtg INNER JOIN tl_member_group g ON g.id=mtg.group_id WHERE mtg.member_id=? AND g.n2g_sync=1'
            )
            ->execute($dc->id)
            ->fetchEach('n2g_group_id');

        /** @type \Model $member */
        $member = \MemberModel::findByPk($dc->id);

        # $member           contains obsolete data (pre save)
        # $dc->activeRecord contains current data

        $recipient = new NewsletterRecipient();
        $recipient->setApiCredentials(self::getApiCredentials());
        $recipient->setListId(self::getListId());

        foreach ($member->row() as $k => $v) {
            switch ($k) {
                case 'email':
                    $recipient->setEmail($v);
                    break;

                case 'phone':
                    $recipient->setPhone($v);
                    break;

                case 'gender':
                    $recipient->setGender($v{0});
                    break;

                case 'firstname':
                    $recipient->setFirstName($v);
                    break;

                case 'lastname':
                    $recipient->setLastName($v);
                    break;

                default:
                    break;
            }
        }

        $recipient->save();

        // Create receiver in these groups
        foreach ($groupsNew as $group) {
            $recipient->addToGroup($group);
        }

        // Delete receiver in these groups
        foreach (array_diff($groupsOld, $groupsNew) as $group) {
            $recipient->removeFromGroup($group);
        }

        return $value;
    }
}
