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

namespace Richardhj\Newsletter2Go\Contao\SyncBundle\Dca;

use Contao\DataContainer;
use Contao\MemberModel;
use Contao\System;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Richardhj\Newsletter2Go\Api\Model\NewsletterRecipient;
use Richardhj\Newsletter2Go\Contao\SyncBundle\AbstractHelper;


/**
 * Class Member
 *
 * @package Richardhj\Newsletter2Go\Contao\Dca
 */
class Member extends AbstractHelper
{

    /**
     * @var Connection
     */
    private $connection;

    /**
     * Member constructor.
     */
    public function __construct()
    {
        $this->connection = System::getContainer()->get('database_connection');
    }

    /**
     * Remove a member from all groups after deleting
     *
     * @category ondelete_callback (table: tl_member)
     *
     * @param $dc
     *
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function deleteMember(DataContainer $dc): void
    {
        if (!$dc->id) {
            return;
        }

        // Check if user has N2G sync enabled
        if (null === ($apiCredentials = self::getApiCredentials())) {
            return;
        }

        $recipient = new NewsletterRecipient();
        $recipient->setApiCredentials($apiCredentials);
        $recipient->setListId(self::getListId());
        $recipient->setEmail($dc->activeRecord->email);

        // Fetch id
        $recipient->save();

        $statement = $this->connection->createQueryBuilder()
            ->select('mg.n2g_group_id')
            ->from('tl_member_group', 'mg')
            ->innerJoin('mg', 'tl_member_to_group', 'mtg', 'mg.id=mtg.group_id')
            ->where('mtg.member_id=:member_id')
            ->andWhere('mg.n2g_sync=1')
            ->setParameter('member_id', $dc->id)
            ->execute();

        foreach ($statement->fetchAll(FetchMode::COLUMN, 0) as $group) {
            $recipient->removeFromGroup($group);
        }
    }

    /**
     * Sync local member with group associated Newsletter2Go groups
     *
     * @category save_callback (field: groups)
     *
     * @param mixed         $value The submitted groups as serialized string
     * @param DataContainer $dc
     *
     * @return mixed
     *
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function syncMemberGroupsWithNewsletter2Go($value, $dc)
    {
        $groups = deserialize($value);

        // Check if user has N2G sync enabled
        if (null === ($apiCredentials = self::getApiCredentials())) {
            return $value;
        }

        $groupsNew = $groups ?
            $this->connection->createQueryBuilder()
                ->select('n2g_group_id')
                ->from('tl_member_group')
                ->where('id IN (:groups)')
                ->andWhere('n2g_sync=1')
                ->setParameter('groups', $groups, Connection::PARAM_STR_ARRAY)
                ->execute()
                ->fetchAll(FetchMode::COLUMN, 0)
            : [];

        $groupsOld = $this->connection->createQueryBuilder()
            ->select('g.n2g_group_id')
            ->from('tl_member_to_group', 'mtg')
            ->innerJoin('mtg', 'tl_member_group', 'g', 'g.id=mtg.group_id')
            ->where('mtg.member_id=:member')
            ->andWhere('n2g_sync=1')
            ->setParameter('member', $dc->id)
            ->execute()
            ->fetchAll(FetchMode::COLUMN, 0);

        // Nothing to sync here
        if (0 === \count($groupsNew) && 0 === \count($groupsOld)) {
            return $value;
        }

        /** @type \Model $member */
        $member = MemberModel::findByPk($dc->id);
        if (null === $member) {
            return $value;
        }

        # $member           contains obsolete data (pre save)
        # $dc->activeRecord contains current data

        $recipient = new NewsletterRecipient();
        $recipient->setApiCredentials($apiCredentials);
        $recipient->setListId(self::getListId());
        $recipient->setId($member->n2g_receiver_id);

        // Todo refactor to event
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

                case 'birthday':
                    $dateOfBirth = date('c', $v);
                    if ($dateOfBirth) {
                        $recipient->setBirthday($dateOfBirth);
                    }
                    break;

                default:
                    break;
            }
        }

        // Saving a recipient will update the data and fetch the id
        $recipient->save();

        // Set the N2G receiver id if not done yet
        if (!$member->n2g_receiver_id) {
            $member->n2g_receiver_id = $recipient->getId();
            $member->save();
        }

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
