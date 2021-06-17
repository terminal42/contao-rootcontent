<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle;

use Composer\InstalledVersions;
use Composer\Semver\VersionParser;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Terminal42\RootcontentBundle\DependencyInjection\Compiler\RootPageContentCompositionPass;

class Terminal42RootcontentBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        if (InstalledVersions::satisfies(new VersionParser(), 'contao/core-bundle', '>= 4.11')) {
            $container->addCompilerPass(new RootPageContentCompositionPass());
        }
    }
}
