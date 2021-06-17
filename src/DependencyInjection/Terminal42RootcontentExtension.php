<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\DependencyInjection;

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Terminal42\RootcontentBundle\EventListener\ArticleOperationListener;
use Terminal42\RootcontentBundle\EventListener\ArticleSectionListener;

class Terminal42RootcontentExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../config')
        );

        $loader->load('services.yml');

        if (InstalledVersions::satisfies(new VersionParser(), 'contao/core-bundle', '< 4.11')) {
            $container->getDefinition(ArticleSectionListener::class)
                ->addTag('contao.callback', [
                    'table' => 'tl_article',
                    'target' => 'list.sorting.paste_button',
                    'method' => 'onPasteButton',
                ])
            ;

            $container
                ->setDefinition(ArticleOperationListener::class, new Definition(ArticleOperationListener::class, [new Reference('security.helper')]))
                ->addTag('contao.callback', [
                    'table' => 'tl_page',
                    'target' => 'list.operations.articles.button',
                    'method' => 'onButtonCallback',
                ])
            ;
        }
    }
}
