<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\Backend;
use Contao\Image;
use Contao\StringUtil;
use Symfony\Component\Security\Core\Security;
use Terminal42\RootcontentBundle\DependencyInjection\Compiler\RootPageContentCompositionPass;

/**
 * @deprecated only used for Contao < 4.11
 */
class ArticleOperationListener
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @see RootPageContentCompositionPass
     */
    public function onButtonCallback(array $row, $href, $label, $title, $icon): string
    {
        if (!$this->security->isGranted('contao_user.modules', 'article')) {
            return '';
        }

        if ('root' === $row['type']) {
            return sprintf(
                '<a href="%s" title="%s">%s</a> ',
                Backend::addToUrl($href.'&amp;node='.$row['id']),
                StringUtil::specialchars($title),
                Image::getHtml($icon, $label)
            );
        }

        return (new \tl_page())->editArticles($row, $href, $label, $title, $icon);
    }
}
