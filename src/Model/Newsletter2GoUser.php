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

namespace Richardhj\Newsletter2Go\Contao\Model;

use Contao\Encryption;
use Contao\Model;


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
    static $strTable = 'tl_newsletter2go_user';

    /**
     * Set a property. Encrypt the auth key beforehand
     *
     * @param string $key
     * @param mixed  $value
     */
    public function __set($key, $value)
    {
        switch ($key) {
            case 'authKey':
                /** @noinspection PhpMissingBreakStatementInspection */
            case 'authRefreshToken':
                $value = Encryption::encrypt($value);

            default:
                parent::__set($key, $value);
        }
    }

    /**
     * Encrypt a property. Decrypt the auth key beforhand
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get($key)
    {
        switch ($key) {
            case 'authKey':
            case 'authRefreshToken':
                return Encryption::decrypt(parent::__get($key));
                break;

            default:
                return parent::__get($key);
        }
    }
}
