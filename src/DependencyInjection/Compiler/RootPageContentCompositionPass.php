<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\DependencyInjection\Compiler;

use Contao\CoreBundle\Controller\Page\RootPageController;
use Contao\CoreBundle\Routing\Page\PageRegistry;
use Contao\CoreBundle\Routing\Page\RouteConfig;
use Contao\FrontendIndex;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Terminal42\RootcontentBundle\Routing\RootPageContentComposition;

class RootPageContentCompositionPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // Support Contao 4.12+
        $controller = \class_exists(RootPageController::class) ? RootPageController::class : FrontendIndex::class.'::renderPage';

        $pageRegistry = $container->getDefinition(PageRegistry::class);

        $pageRegistry->addMethodCall('add', [
            'root',
            new Definition(RouteConfig::class, [
                null,
                null,
                null,
                [],
                [],
                ['_controller' => $controller],
            ]),
            null,
            $container->getDefinition(RootPageContentComposition::class),
        ]);
    }
}
