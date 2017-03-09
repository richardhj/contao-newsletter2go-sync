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


use Haste\Form\Form;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Newsletter2Go\ContaoSync\AbstractHelper;
use Newsletter2Go\ContaoSync\Model\Newsletter2GoUser as UserModel;
use Newsletter2Go\OAuth2\Client\Provider\Newsletter2Go as OAuthProvider;

class Newsletter2GoUser extends AbstractHelper
{

    /**
     * Display and process a form that authenticate the api user by fetching a refresh_token for given user credentials
     *
     * @param \DataContainer $dc
     *
     * @return string
     */
    public function authenticateUser(\DataContainer $dc)
    {
        if (!$dc->id) {
            return '';
        }

        $return = '';

        $user         = UserModel::findByPk($dc->id);
        $authKey      = $user->authKey;
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

                return 'You are logged in as: ' . $resourceOwner['first_name'] . ' ' . $resourceOwner['last_name'];

            } catch (IdentityProviderException $e) {
                $user->authRefreshToken = null;
                $user->save();

                \Controller::reload();
            }

            return '';
        }

        define('BYPASS_TOKEN_CHECK', true);

        $form = new Form(
            'authenticate_n2g_user', 'POST', function ($haste) {
            /** @noinspection PhpUndefinedMethodInspection */
            return $haste->getFormId() === \Input::post('FORM_SUBMIT');
        }
        );

        $form->addFormField(
            'username',
            [
                'label'     => 'Benutzername',
                'inputType' => 'text',
                'eval'      => [
                    'mandatory' => true
                ],
            ]
        );

        $form->addFormField(
            'password',
            [
                'label'     => ['Passwort'],
                'inputType' => 'text',
                'eval'      => [
                    'mandatory' => true,
                    'hideInput' => true
                ],
            ]
        );

        // Let's add  a submit button
        $form->addFormField(
            'submit',
            [
                'label'     => 'Authentifizieren',
                'inputType' => 'submit',
            ]
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
            } catch (IdentityProviderException $e) {
                $form->getWidget('password')->addError($e->getResponseBody()['error_description']);
            }

        }

        $userAuthTemplate = new \FrontendTemplate('be_auth_user');
        $form->addToTemplate($userAuthTemplate);
        $return .= $userAuthTemplate->parse();

        return $return;
    }
}
