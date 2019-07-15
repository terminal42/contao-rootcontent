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

namespace Terminal42\RootcontentBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Terminal42RootcontentExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yml');
    }
}
