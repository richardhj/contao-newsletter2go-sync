<?php
/**
 * Newsletter2Go Synchronization for Contao Open Source CMS
 *
 * Copyright (c) 2015-2017 Richard Henkenjohann
 *
 * @package Newsletter2GoSync
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */

namespace Newsletter2Go\ContaoSync;


use Newsletter2Go\Api\Model\NewsletterGroup;
use Newsletter2Go\Api\Model\NewsletterList;
use Newsletter2Go\Api\Tool\ApiCredentials;
use Newsletter2Go\Api\Tool\ApiCredentialsFactory;
use Newsletter2Go\ContaoSync\Model\Newsletter2GoUser;


abstract class AbstractHelper
{

    /**
     * Get all Newsletter2Go groups
     *
     * @category options_callback
     *
     * @return array
     */
    public function getNewsletter2GoGroups()
    {
        $return = [];
        $groups = NewsletterGroup::findByList(self::getListId(), null, self::getApiCredentials());

        if (null !== $groups) {
            /** @var NewsletterGroup $group */
            foreach ($groups as $group) {
                $return[$group->getId()] = $group->getName();
            }
        }

        return $return;
    }


    /**
     * Get the id of the first Newsletter2Go list which might be the default list
     *
     * @return string
     */
    protected static function getListId()
    {
        /** @var NewsletterList[] $lists */
        $lists = NewsletterList::findAll(null, self::getApiCredentials());

        return $lists[0]->getId();
    }


    /**
     * @return ApiCredentials|null
     */
    protected static function getApiCredentials()
    {
        /** @var \BackendUser|\User $backendUser */
        $backendUser = \BackendUser::getInstance();

        if (!$backendUser->n2g_active) {
            return null;
        }

        $user = Newsletter2GoUser::findByPk($backendUser->n2g_user);

        if (null === $user) {
            return null;
        }

        return ApiCredentialsFactory::create($user->authKey, $user->authRefreshToken);
    }
}
