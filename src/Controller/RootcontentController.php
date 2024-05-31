<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\Controller;

use Contao\ArticleModel;
use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsFrontendModule;
use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\Date;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsFrontendModule(template: 'mod_rootcontent')]
class RootcontentController extends AbstractFrontendModuleController
{
    public function __construct(private readonly TokenChecker $tokenChecker)
    {
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $pageModel = $this->getPageModel();

        if (!$pageModel instanceof PageModel) {
            return new Response('');
        }

        $article = $this->getArticle((int) $pageModel->rootId, $model->rootcontent);

        if (null === $article) {
            return new Response('');
        }

        $template->article = Controller::getArticle($article, false, true, $template->inColumn);

        return $template->getResponse();
    }

    private function getArticle(int $rootPageId, string $section): ArticleModel|null
    {
        $cols = ['tl_article.pid=?', 'tl_article.title=?'];

        if (!$this->tokenChecker->isPreviewMode()) {
            $time = Date::floorToMinute();
            $cols[] = "tl_article.published='1' AND (tl_article.start='' OR tl_article.start<$time) AND (tl_article.stop='' OR tl_article.stop>$time)";
        }

        return ArticleModel::findOneBy(
            $cols,
            [$rootPageId, $section],
        );
    }
}
