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

namespace Richardhj\Newsletter2Go\Contao\SyncBundle\Model;

use Contao\{Model, System};
use ParagonIE\Halite\Alerts\CannotPerformOperation;
use ParagonIE\Halite\Alerts\InvalidKey;
use ParagonIE\Halite\KeyFactory;
use ParagonIE\Halite\Symmetric\Crypto as SymmetricCrypto;
use ParagonIE\Halite\Symmetric\EncryptionKey;
use ParagonIE\HiddenString\HiddenString;


/**
 * Class Newsletter2GoUser
 *
 * @property string $name
 * @property string $authKey          Encrypted account auth key
 * @property string $authRefreshToken Encrypted user refresh token
 * @package Richardhj\Newsletter2Go\Contao\Model
 */
class Newsletter2GoUser extends Model
{

    /**
     * {@inheritdoc}
     */
    protected static $strTable = 'tl_newsletter2go_user';

    /**
     * Set a property. Encrypt the auth key beforehand
     *
     * @param string $key
     * @param mixed  $value
     *
     * @throws CannotPerformOperation
     * @throws InvalidKey
     * @throws \ParagonIE\Halite\Alerts\InvalidDigestLength
     * @throws \ParagonIE\Halite\Alerts\InvalidMessage
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     */
    public function __set($key, $value)
    {
        switch ($key) {
            case 'authKey':
                /** @noinspection PhpMissingBreakStatementInspection */
            case 'authRefreshToken':
                $value = SymmetricCrypto::encrypt(new HiddenString($value), $this->getEncryptionKey());

            default:
                parent::__set($key, $value);
        }
    }

    /**
     * Encrypt a property. Decrypt the auth key beforehand
     *
     * @param string $key
     *
     * @return mixed
     * @throws CannotPerformOperation
     * @throws InvalidKey
     * @throws \ParagonIE\Halite\Alerts\InvalidDigestLength
     * @throws \ParagonIE\Halite\Alerts\InvalidMessage
     * @throws \ParagonIE\Halite\Alerts\InvalidSignature
     * @throws \ParagonIE\Halite\Alerts\InvalidType
     */
    public function __get($key)
    {
        switch ($key) {
            case 'authKey':
            case 'authRefreshToken':
                return !empty(parent::__get($key)) ? SymmetricCrypto::decrypt(
                    parent::__get($key),
                    $this->getEncryptionKey()
                )->getString() : '';
                break;

            default:
                return parent::__get($key);
        }
    }

    /**
     * @throws InvalidKey
     * @throws CannotPerformOperation
     */
    private function getEncryptionKey(): EncryptionKey
    {
        $keyPath =
            System::getContainer()->getParameter('kernel.project_dir') . '/var/contao-newsletter2go-sync.secret.key';
        try {
            $key = KeyFactory::loadEncryptionKey($keyPath);
        } catch (CannotPerformOperation $e) {
            $key = KeyFactory::generateEncryptionKey();
            KeyFactory::save($key, $keyPath);
        }

        return $key;
    }
}
