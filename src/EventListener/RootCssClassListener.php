<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use Contao\LayoutModel;
use Contao\PageModel;

/**
 * Prepends the css class of the root page.
 */
#[AsHook('generatePage')]
class RootCssClassListener
{
    public function __invoke(PageModel $objPage, LayoutModel $objLayout): void
    {
        $rootPage = PageModel::findByPk($objPage->rootId);

        if (null !== $rootPage && $cssClass = $rootPage->cssClass) {
            $objLayout->cssClass = $cssClass.' '.$objLayout->cssClass;
        }
    }
}
