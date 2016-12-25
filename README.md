# Contao Newsletter2Go Synchronization

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]]()
[![Dependency Status][ico-dependencies]][link-dependencies]

Synchronize recipients between Contao and Newsletter2Go. Each member groups and recipients of Contao's native newsletter system can be synchronized into Newsletter2Go groups. 

## Install

### Via Composer

``` bash
$ composer require richardhj/contao-newsletter2go-sync
```

## Usage

* Go to the back end section "Newsletter2Go Users" and create a user. After saving, click on "authenticate"
* Edit your user you are accessing the back end with. Choose the "Newsletter2Go User" and click on save
* Edit a member group or newsletter channel and activate syncing

Syncing will work for back end users that have a "Newsletter2Go user" assigned exclusively.

## License

The  GNU Lesser General Public License (LGPL).

[ico-version]: https://img.shields.io/packagist/v/richardhj/contao-newsletter2go-sync.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-LGPL-brightgreen.svg?style=flat-square
[ico-dependencies]: https://www.versioneye.com/php/richardhj:contao-newsletter2go-sync/badge.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/richardhj/contao-newsletter2go-sync
[link-dependencies]: https://www.versioneye.com/php/richardhj:contao-newsletter2go-sync
