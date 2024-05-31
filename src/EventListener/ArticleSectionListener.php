<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * If the article is in a root page, we show a select menu instead of article name.
 */
#[AsCallback(table: 'tl_article', target: 'config.onload')]
class ArticleSectionListener
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly Connection $database,
    ) {
    }

    public function __invoke(DataContainer $dc): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request || 'edit' !== $request->query->get('act')) {
            return;
        }

        $page = $this->getPage((int) $dc->id);

        if ('root' === $page['type']) {
            $GLOBALS['TL_DCA']['tl_article']['palettes']['default'] = '{title_legend},title,author;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID;{publish_legend},published,start,stop';

            $theme = $this->getTheme((int) $page['layout']);

            $sections = array_diff(
                StringUtil::deserialize($theme['rootcontent'], true),
                $this->getExistingSections((int) $page['id'], (int) $dc->id),
            );

            $GLOBALS['TL_DCA']['tl_article']['fields']['title'] = [
                'label' => &$GLOBALS['TL_LANG']['tl_article']['rootcontent'],
                'inputType' => 'select',
                'options' => array_values($sections),
                'eval' => ['mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            ];
        }
    }

    private function getPage(int $articleId): array|null
    {
        $qb = $this->database->createQueryBuilder();
        $qb
            ->select('tl_page.*')
            ->from('tl_article')
            ->leftJoin('tl_article', 'tl_page', 'tl_page', 'tl_article.pid=tl_page.id')
            ->where('tl_article.id = :articleId')
            ->setParameter('articleId', $articleId)
        ;

        return $qb->fetchAssociative() ?: null;
    }

    private function getTheme(int $layoutId): array|null
    {
        $qb = $this->database->createQueryBuilder();
        $qb
            ->select('tl_theme.*')
            ->from('tl_theme')
            ->leftJoin('tl_theme', 'tl_layout', 'tl_layout', 'tl_theme.id=tl_layout.pid')
            ->where('tl_layout.id = :layout_id')
            ->setParameter('layout_id', $layoutId)
        ;

        return $qb->fetchAssociative() ?: null;
    }

    private function getExistingSections(int $pageId, int $articleId = 0): array
    {
        $qb = $this->database->createQueryBuilder();
        $qb
            ->select('title')
            ->from('tl_article')
            ->where('pid = :page_id')
            ->andWhere('id != :article_id')
            ->setParameter('page_id', $pageId)
            ->setParameter('article_id', $articleId)
        ;

        return $qb->fetchFirstColumn();
    }
}
