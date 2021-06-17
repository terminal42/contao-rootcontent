<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\Backend;
use Contao\BackendUser;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class ArticleSectionListener
{
    private RequestStack $requestStack;
    private Connection $database;
    private Security $security;

    public function __construct(RequestStack $requestStack, Connection $database, Security $security)
    {
        $this->requestStack = $requestStack;
        $this->database = $database;
        $this->security = $security;
    }

    /**
     * If the article is in a root page, we show a select menu instead of article name.
     *
     * @Callback(table="tl_article", target="config.onload")
     */
    public function onLoad(DataContainer $dc): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request || 'edit' !== $request->query->get('act')) {
            return;
        }

        $page = $this->getPage($dc->id);

        if ('root' === $page['type']) {
            $GLOBALS['TL_DCA']['tl_article']['palettes']['default'] = '{title_legend},title,author;{expert_legend:hide},cssID;{publish_legend},published';

            $theme = $this->getTheme((int) $page['layout']);

            $sections = array_diff(
                StringUtil::deserialize($theme['rootcontent'], true),
                $this->getExistingSections((int) $page['id'], (int) $dc->id)
            );

            $GLOBALS['TL_DCA']['tl_article']['fields']['title'] = [
                'label' => &$GLOBALS['TL_LANG']['tl_article']['rootcontent'],
                'inputType' => 'select',
                'options' => array_values($sections),
                'eval' => ['mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            ];
        }
    }

    /**
     * Overrides parent function, allow paste for root pages.
     *
     * @param mixed $circularReference
     * @param mixed $arrClipboard
     *
     * @deprecated only used for Contao < 4.11
     */
    public function onPasteButton(DataContainer $dc, array $row, string $table, $circularReference, $arrClipboard = false): string
    {
        $user = $this->security->getUser();

        if ('tl_page' === $table
            && 'root' === $row['type']
            && $user instanceof BackendUser
            && ($user->isAdmin || $user->isAllowed(5, $row))
            && !$circularReference
        ) {
            $theme = $this->getTheme((int) $row['layout']);

            $sections = array_diff(
                StringUtil::deserialize($theme['rootcontent'], true),
                $this->getExistingSections((int) $row['id'])
            );

            if (!empty($sections)) {
                return '<a href="'.Backend::addToUrl('act='.$arrClipboard['mode'].'&amp;mode=2&amp;pid='.$row['id'].(!\is_array($arrClipboard['id']) ? '&amp;id='.$arrClipboard['id'] : '')).'" title="'.StringUtil::specialchars(sprintf($GLOBALS['TL_LANG'][$dc->table]['pasteinto'][1], $row['id'])).'" onclick="Backend.getScrollOffset();">'.Backend::generateImage('pasteinto.gif', sprintf($GLOBALS['TL_LANG'][$dc->table]['pasteinto'][1], $row['id']), 'class="blink"').'</a> ';
            }

            return Backend::generateImage('pasteinto_.gif', '', 'class="blink"').' ';
        }

        if ('tl_article' === $table) {
            $page = $this->getPage((int) $row['id']);

            if ('root' === $page['type']) {
                return Backend::generateImage('pasteafter_.gif', '', 'class="blink"').' ';
            }
        }

        return (new \tl_article())->pasteArticle($dc, $row, $table, $circularReference, $arrClipboard);
    }

    private function getPage($articleId): ?array
    {
        $qb = $this->database->createQueryBuilder();

        $qb
            ->select('tl_page.*')
            ->from('tl_article')
            ->leftJoin('tl_article', 'tl_page', 'tl_page', 'tl_article.pid=tl_page.id')
            ->where('tl_article.id = :articleId')
            ->setParameter('articleId', $articleId)
        ;

        return $qb->execute()->fetchAssociative() ?: null;
    }

    private function getTheme(int $layoutId): ?array
    {
        $qb = $this->database->createQueryBuilder();

        $qb
            ->select('tl_theme.*')
            ->from('tl_theme')
            ->leftJoin('tl_theme', 'tl_layout', 'tl_layout', 'tl_theme.id=tl_layout.pid')
            ->where('tl_layout.id = :layout_id')
            ->setParameter('layout_id', $layoutId)
        ;

        return $qb->execute()->fetchAssociative() ?: null;
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

        return $qb->execute()->fetchFirstColumn();
    }
}
