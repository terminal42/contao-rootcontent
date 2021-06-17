<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\LayoutModel;
use Contao\PageModel;

class RootCssClassListener
{
    /**
     * Prepends the css class of the root page.
     *
     * @Hook("generatePage")
     */
    public function onGeneratePage(PageModel $objPage, LayoutModel $objLayout): void
    {
        $rootPage = PageModel::findByPk($objPage->rootId);

        if (null !== $rootPage && $cssClass = $rootPage->cssClass) {
            $objLayout->cssClass = $cssClass.' '.$objLayout->cssClass;
        }
    }
}
