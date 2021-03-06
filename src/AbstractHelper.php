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

namespace Richardhj\Newsletter2Go\Contao\SyncBundle;


use Contao\BackendUser;
use Contao\Config;
use Richardhj\Newsletter2Go\Api\Model\NewsletterGroup;
use Richardhj\Newsletter2Go\Api\Model\NewsletterList;
use Richardhj\Newsletter2Go\Api\Tool\ApiCredentials;
use Richardhj\Newsletter2Go\Api\Tool\ApiCredentialsFactory;
use Richardhj\Newsletter2Go\Contao\SyncBundle\Model\Newsletter2GoUser;


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
    public function getNewsletter2GoGroups(): array
    {
        $return = [];
        try {
            $groups = NewsletterGroup::findByList(self::getListId(), null, self::getApiCredentials());

            if (null !== $groups) {
                /** @var NewsletterGroup $group */
                foreach ($groups as $group) {
                    $return[$group->getId()] = $group->getName();
                }
            }
        } catch (\Exception $e) {
            $return[] = 'Error: '.$e->getMessage().' Did you check the API user authentication?';
        }

        return $return;
    }

    /**
     * Get the id of the first Newsletter2Go list which might be the default list
     *
     * @return string
     *
     * @throws \RuntimeException
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    protected static function getListId(): string
    {
        /** @var NewsletterList[] $lists */
        $lists = NewsletterList::findAll(null, self::getApiCredentials());

        // TODO: add support for multiple lists (= address books in N2G). Should be done via the tl_n2g_user or tl_settings dca

        return $lists[0]->getId();
    }

    /**
     * @return ApiCredentials|null
     */
    protected static function getApiCredentials(): ?ApiCredentials
    {
        /** @var BackendUser|\User $backendUser */
        $backendUser = BackendUser::getInstance();
        if ($backendUser->n2g_active) {
            $user = Newsletter2GoUser::findByPk($backendUser->n2g_user);
            if (null === $user) {
                return null;
            }
        } else {
            $user = Newsletter2GoUser::findByPk(Config::get('n2g_default_user'));
        }

        return ApiCredentialsFactory::createFromRefreshToken($user->authKey, $user->authRefreshToken);
    }
}
