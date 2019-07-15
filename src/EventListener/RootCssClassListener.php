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

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\LayoutModel;
use Contao\PageModel;

class RootCssClassListener
{
    /**
     * Prepends the css class of the root page.
     *
     * @param PageModel   $objPage
     * @param LayoutModel $objLayout
     */
    public function onGeneratePage(PageModel $objPage, LayoutModel $objLayout): void
    {
        $rootPage = PageModel::findByPk($objPage->rootId);
        if (null !== $rootPage && $cssClass = $rootPage->cssClass) {
            $objLayout->cssClass = $cssClass.' '.$objLayout->cssClass;
        }
    }
}
