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

use Contao\DataContainer;
use Richardhj\Newsletter2Go\Contao\AbstractHelper;


/**
 * Class NewsletterChannel
 *
 * @package Richardhj\Newsletter2Go\Contao\Dca
 */
class NewsletterChannel extends AbstractHelper
{
    /**
     * Sync (create and delete) local newsletter channels with Newsletter2Go groups
     *
     * @category onload_callback
     *
     * @param DataContainer $dc
     */
    public function syncNewsletterChannelsWithGroups(DataContainer $dc)
    {
//        // Only synchronize in list view
//        if ($dc->id) {
//            return;
//        }
//
//        $groups = Groups::getInstance()->getAll();
//
//        if (null === $groups) {
//            return;
//        }
//
//        // Create groups
//        foreach ($groups as $group) {
//            if (null === ($channelExisting = \NewsletterChannelModel::findBy('cr_group_id', $group->id))) {
//                /** @type \Model $channelNew */
//                $channelNew = new \NewsletterChannelModel();
//                $channelNew->title = $group->name;
//                $channelNew->tstamp = $group->stamp;
//                $channelNew->cr_group_id = $group->id;
//                $channelNew->save();
//            } else {
//                $channelExisting->title = $group->name;
//                $channelExisting->tstamp = $group->stamp;
//                $channelExisting->save();
//            }
//        }
////
////
////        $channels = \NewsletterChannelModel::findBy(['cr_group_id<>0'], []);
////        $toDelete = array_diff($groups)
//        // Delete groups
    }
}