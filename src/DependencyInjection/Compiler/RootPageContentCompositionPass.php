<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\DependencyInjection\Compiler;

use Contao\CoreBundle\Controller\Page\RootPageController;
use Contao\CoreBundle\Routing\Page\PageRegistry;
use Contao\CoreBundle\Routing\Page\RouteConfig;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Terminal42\RootcontentBundle\Routing\RootPageContentComposition;

class RootPageContentCompositionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $pageRegistry = $container->findDefinition(PageRegistry::class);

        $pageRegistry->addMethodCall('add', [
            'root',
            new Definition(RouteConfig::class, [
                null,
                null,
                null,
                [],
                [],
                ['_controller' => RootPageController::class],
            ]),
            null,
            $container->getDefinition(RootPageContentComposition::class),
        ]);
    }
}
