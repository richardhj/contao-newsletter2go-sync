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
use Contao\MemberGroupModel;
use Contao\System;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Richardhj\Newsletter2Go\Api\Model\NewsletterGroup;
use Richardhj\Newsletter2Go\Api\Model\NewsletterRecipient;
use Richardhj\Newsletter2Go\Contao\SyncBundle\AbstractHelper;


/**
 * Class MemberGroup
 *
 * @package Richardhj\Newsletter2Go\Contao\Dca
 */
class MemberGroup extends AbstractHelper
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
     * Create a group on Newsletter2Go if sync for member group was enabled but no existing group was selected
     *
     * @category onsubmit_callback (table: tl_member_group)
     *
     * @param DataContainer $dc
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function createNewsletter2GoGroup(DataContainer $dc): void
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
     *
     * @throws \RuntimeException
     * @throws \LogicException
     */
    public function deleteMemberGroup(DataContainer $dc): void
    {
        if (!$dc->id || !$dc->activeRecord->n2g_sync || !$dc->activeRecord->n2g_group_id) {
            return;
        }

        // Check if user has N2G sync enabled
        if (null === ($apiCredentials = self::getApiCredentials())) {
            return;
        }

        $members = $this->connection->createQueryBuilder()
            ->select('m.email AS email', 'm.n2g_receiver_id AS n2g_receiver_id')
            ->from('tl_member', 'm')
            ->innerJoin('m', 'tl_member_to_group', 'mg', 'm.id=mg.member_id')
            ->where('mg.group_id=:group')
            ->setParameter('group', $dc->id)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        foreach ($members as $member) {
            $recipient = new NewsletterRecipient();
            $recipient->setApiCredentials($apiCredentials);
            $recipient->setListId(self::getListId());
            if ($member['n2g_receiver_id']) {
                $recipient->setId($member['n2g_receiver_id']);
            } else {
                $recipient->setEmail($member['email']);
            }
            // Saving a recipient will fetch the id
            $recipient->save();

            $recipient->removeFromGroup($dc->activeRecord->n2g_group_id);
        }
    }
}
