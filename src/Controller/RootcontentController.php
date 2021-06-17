<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\Controller;

use Contao\ArticleModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Date;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @FrontendModule(category="miscellaneous")
 */
class RootcontentController extends AbstractFrontendModuleController
{
    private TokenChecker $tokenChecker;

    public function __construct(TokenChecker $tokenChecker)
    {
        $this->tokenChecker = $tokenChecker;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $pageModel = $request->attributes->get('pageModel');

        if (!$pageModel instanceof PageModel) {
            return new Response('');
        }

        $article = $this->getArticle($pageModel->rootId, $model->rootcontent);

        if (null === $article) {
            return new Response('');
        }

        $template->article = Controller::getArticle($article, false, true, $template->inColumn);

        return $template->getResponse();
    }

    private function getArticle($rootPageId, $section): ?ArticleModel
    {
        $cols = ['tl_article.pid=?', 'tl_article.title=?'];

        if (!$this->tokenChecker->isPreviewMode()) {
            $time = Date::floorToMinute();
            $cols[] = "tl_article.published='1' AND (tl_article.start='' OR tl_article.start<$time) AND (tl_article.stop='' OR tl_article.stop>$time)";
        }

        return ArticleModel::findOneBy(
            $cols,
            [$rootPageId, $section]
        );
    }
}
