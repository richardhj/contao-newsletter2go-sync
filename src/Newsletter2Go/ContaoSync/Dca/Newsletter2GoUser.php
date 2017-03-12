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

        $user         = UserModel::findByPk($dc->id);
        $authKey      = $user->authKey;
        $refreshToken = $user->authRefreshToken;

        $userAuthTemplate             = new \FrontendTemplate('be_auth_user');
        $userAuthTemplate->backBtHref = ampersand(str_replace('&key=authenticate', '', \Environment::get('request')));
        $userAuthTemplate->bacBtTitle = specialchars($GLOBALS['TL_LANG']['MSC']['backBTTitle']);
        $userAuthTemplate->backBt     = $GLOBALS['TL_LANG']['MSC']['backBT'];
        $userAuthTemplate->headline   = 'Authentifizierung des API-Benutzers';

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

                $userAuthTemplate->fields =
                    '<p class="tl_confirm m12">You are logged in as: '
                    . $resourceOwner['first_name'] . ' ' . $resourceOwner['last_name'] . '</p>';
                return $userAuthTemplate->parse();

            } catch (IdentityProviderException $e) {
                $user->authRefreshToken = null;
                $user->save();

                \Controller::reload();
            }

            return '';
        }

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

        $form->addToTemplate($userAuthTemplate);
        $userAuthTemplate->action = ampersand(\Environment::get('request'), true);
        $userAuthTemplate->submit = specialchars('Authentifizieren');
        $userAuthTemplate->tip    = 'Ihre Zugangsdaten (Benuztername/Passwort) werden nicht gespeichert';

        return $userAuthTemplate->parse();
    }
}
