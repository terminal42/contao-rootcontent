<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Terminal42\RootcontentBundle\Terminal42RootcontentBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(Terminal42RootcontentBundle::class)->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
