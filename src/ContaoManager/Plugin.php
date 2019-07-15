<?php

declare(strict_types=1);

/*
 * rootcontent extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2019, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/terminal42/contao-rootcontent
 */

namespace Terminal42\RootcontentBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Terminal42\RootcontentBundle\Terminal42RootcontentBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(Terminal42RootcontentBundle::class)->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
