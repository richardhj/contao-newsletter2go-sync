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

namespace Richardhj\Newsletter2Go\Contao;


use Contao\BackendUser;
use Richardhj\Newsletter2Go\Api\Model\NewsletterGroup;
use Richardhj\Newsletter2Go\Api\Model\NewsletterList;
use Richardhj\Newsletter2Go\Api\Tool\ApiCredentials;
use Richardhj\Newsletter2Go\Api\Tool\ApiCredentialsFactory;
use Richardhj\Newsletter2Go\Contao\Model\Newsletter2GoUser;


/**
 * Class AbstractHelper
 *
 * @package Richardhj\Newsletter2Go\Contao
 */
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

        // TODO: add support for multiple lists (= address books in N2G). Should be done via the tl_n2g_user or tl_settings dca

        return $lists[0]->getId();
    }

    /**
     * @return ApiCredentials|null
     */
    protected static function getApiCredentials()
    {
        /** @var BackendUser|\User $backendUser */
        $backendUser = BackendUser::getInstance();

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
