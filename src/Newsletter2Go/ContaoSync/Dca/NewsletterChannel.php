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


use Newsletter2Go\ContaoSync\AbstractHelper;

class NewsletterChannel extends AbstractHelper
{
    /**
     * Sync (create and delete) local newsletter channels with Newsletter2Go groups
     *
     * @category onload_callback
     *
     * @param \DataContainer $dc
     */
    public function syncNewsletterChannelsWithGroups(\DataContainer $dc)
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