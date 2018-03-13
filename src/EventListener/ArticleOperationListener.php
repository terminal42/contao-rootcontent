<?php

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Image;
use Contao\StringUtil;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ArticleOperationListener
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * Constructor.
     *
     * @param TokenStorage $tokenStorage
     */
    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onButtonCallback(array $row, string $href, string $label, string $title, string $icon): string
    {
        $user = ($token = $this->tokenStorage->getToken()) ? $token->getUser() : null;

        if (!$user instanceof BackendUser || !$user->hasAccess('article', 'modules')) {
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
