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

use Contao\Backend;
use Contao\BackendUser;
use Contao\Image;
use Contao\StringUtil;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ArticleOperationListener
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Constructor.
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onButtonCallback(array $row, $href, $label, $title, $icon): string
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
