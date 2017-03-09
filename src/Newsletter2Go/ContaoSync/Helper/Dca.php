<?php
/**
 * Newsletter2Go Synchronization for Contao Open Source CMS
 *
 * Copyright (c) 2015-2017 Richard Henkenjohann
 *
 * @package Newsletter2GoSync
 * @author  Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 */

namespace Newsletter2Go\ContaoSync\Helper;


use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Newsletter2Go\Api\Model\NewsletterGroup;
use Newsletter2Go\Api\Model\NewsletterRecipient;
use Newsletter2Go\ContaoSync\Helper;
use Newsletter2Go\ContaoSync\Model\Newsletter2GoUser;
use Newsletter2Go\OAuth2\Client\Provider\Newsletter2Go as OAuthProvider;


class Dca extends Helper
{

    /**
     * Get all cleverreach groups
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
            $recipient->save();

            $recipient->removeFromGroup($dc->activeRecord->n2g_group_id);
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


    /**
     * Create a group on Newsletter2Go if sync for member group was enabled but no existing group was selected
     * @category onsubmit_callback (table: tl_member_group)
     *
     * @param \DataContainer $dc
     */
    public function createN2GGroupForMemberGroup(\DataContainer $dc)
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


    public function authenticateUser(\DataContainer $dc)
    {
        if (!$dc->id) {
            return '';
        }

        $return = '';

        $user = Newsletter2GoUser::findByPk($dc->id);
        $authKey = $user->authKey;
        $refreshToken = $user->authRefreshToken;

        $provider = new OAuthProvider(
            [
                'authKey' => $authKey,
            ]
        );

        if ($refreshToken) {
            // Test current refresh_token
            try {
                $accessToken = $provider->getAccessToken(
                    'https://nl2go.com/jwt_refresh',
                    [
                        'refresh_token' => $refreshToken,
                    ]
                );

                $resourceOwner = $provider->getResourceOwner($accessToken)->toArray();

                return 'You are logged in as: '.$resourceOwner['first_name'].' '.$resourceOwner['last_name'];

            } catch (IdentityProviderException $e) {
                $user->authRefreshToken = null;
                $user->save();

                \Controller::reload();
            }

            return '';
        }

        define('BYPASS_TOKEN_CHECK', true);

        $form = new \Haste\Form\Form(
            'authenticate_n2g_user', 'POST', function ($haste) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $haste->getFormId() === \Input::post('FORM_SUBMIT');
        }
        );

        $form->addFormField(
            'username',
            array(
                'label'     => 'Benutzername',
                'inputType' => 'text',
                'eval'      => array('mandatory' => true),
            )
        );

        $form->addFormField(
            'password',
            array(
                'label'     => array('Passwort'),
                'inputType' => 'text',
                'eval'      => array('mandatory' => true, 'hideInput' =>true),
            )
        );

        // Let's add  a submit button
        $form->addFormField(
            'submit',
            array(
                'label'     => 'Authentifizieren',
                'inputType' => 'submit',
            )
        );

        // validate() also checks whether the form has been submitted
        if ($form->validate()) {

            try {
                // Login and fetch new access token
                $accessToken = $provider->getAccessToken(
                    'https://nl2go.com/jwt',
                    [
                        'username' => $form->fetch('username'),
                        'password' => $form->fetch('password'),
                    ]
                );

            $user->authRefreshToken = $accessToken->getRefreshToken();
            $user->save();

            \Controller::reload();
            }
            catch (IdentityProviderException $e) {
                $form->getWidget('password')->addError($e->getResponseBody()['error_description']);
            }

        }

        $objMyTemplate = new \FrontendTemplate('be_auth_user');
        $form->addToTemplate($objMyTemplate);
        $return .= $objMyTemplate->parse();

        return $return;
    }


    /**
     * Sync (create and delete) local newsletter channels with cleverreach groups
     * @category onload_callback
     *
     * @param \DataContainer $dc
     */
    public
    function syncNewsletterChannelsWithGroups(
        \DataContainer $dc
    ) {
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
