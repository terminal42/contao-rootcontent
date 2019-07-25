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

namespace Terminal42\RootcontentBundle\Controller;

use Contao\ArticleModel;
use Contao\BackendTemplate;
use Contao\CoreBundle\Framework\ContaoFrameworkInterface;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\Security\Authentication\Token\FrontendPreviewToken;
use Contao\Date;
use Contao\ModuleArticle;
use Contao\ModuleModel;
use Contao\PageModel;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RootcontentController
{
    /**
     * @var ContaoFrameworkInterface
     */
    private $framework;

    /**
     * @var ScopeMatcher
     */
    private $scopeMatcher;

    /**
     * @var Connection
     */
    private $database;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Constructor.
     *
     * @param ContaoFrameworkInterface $framework
     * @param ScopeMatcher             $scopeMatcher
     * @param Connection               $database
     * @param TokenStorageInterface    $tokenStorage
     */
    public function __construct(ContaoFrameworkInterface $framework, ScopeMatcher $scopeMatcher, Connection $database, TokenStorageInterface $tokenStorage)
    {
        $this->framework = $framework;
        $this->scopeMatcher = $scopeMatcher;
        $this->database = $database;
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(Request $request, ModuleModel $module, string $section)
    {
        if ($this->scopeMatcher->isBackendRequest($request)) {
            $template = new BackendTemplate('be_wildcard');

            $template->wildcard = '### ROOT CONTENT ###';
            $template->id = $module->id;
            $template->link = $module->name;
            $template->href = $request->getBaseUrl().'?do=themes&amp;table=tl_module&amp;act=edit&amp;id='.$module->id;

            return $template->getResponse();
        }

        $this->framework->initialize();

        /* @var PageModel $objPage */
        global $objPage;

        $article = $this->getArticle($objPage->rootId, $module->rootcontent);

        if (null === $article) {
            return new Response('', Response::HTTP_NO_CONTENT);
        }

        $module = $this->framework->createInstance(ModuleArticle::class, [$article, $section]);

        return new Response($module->generate(true));
    }

    private function getArticle($rootPageId, $section): ?ArticleModel
    {
        /** @var ArticleModel $repository */
        $repository = $this->framework->getAdapter(ArticleModel::class);

        $cols = ['tl_article.pid=?', 'tl_article.title=?'];

        $token = $this->tokenStorage->getToken();

        if (!$token instanceof FrontendPreviewToken || !$token->showUnpublished()) {
            $time = Date::floorToMinute();
            $cols[] = "tl_article.published='1' AND (tl_article.start='' OR tl_article.start<$time) AND (tl_article.stop='' OR tl_article.stop>$time)";
        }

        return $repository->findOneBy(
            $cols,
            [$rootPageId, $section]
        );
    }
}
