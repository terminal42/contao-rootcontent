<?php

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\Backend;
use Contao\BackendUser;
use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ArticleSectionListener
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Connection
     */
    private $database;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * Constructor.
     *
     * @param RequestStack $requestStack
     * @param Connection   $database
     * @param TokenStorage $tokenStorage
     */
    public function __construct(RequestStack $requestStack, Connection $database, TokenStorage $tokenStorage)
    {
        $this->requestStack = $requestStack;
        $this->database = $database;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * If the article is in a root page, we show a select menu instead of article name
     */
    public function onLoad(DataContainer $dc)
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null === $request || 'edit' !== $request->query->get('act')) {
            return;
        }

        $page = $this->getPage($dc->id);

        if ('root' === $page['type']) {
            $GLOBALS['TL_DCA']['tl_article']['palettes']['default'] = '{title_legend},title,author;{expert_legend:hide},cssID;{publish_legend},published';

            $theme = $this->getTheme($page['layout']);

            $sections = array_diff(
                StringUtil::deserialize($theme['rootcontent'], true),
                $this->getExistingSections((int) $page['id'], $dc->id)
            );

            $GLOBALS['TL_DCA']['tl_article']['fields']['title'] = array
            (
                'label'		=> &$GLOBALS['TL_LANG']['tl_article']['rootcontent'],
                'inputType'	=> 'select',
                'options'	=> array_values($sections),
                'eval'		=> array('mandatory'=>true, 'includeBlankOption'=>true),
            );
        }
    }

    /**
     * Overrides parent function, allow paste for root pages
     */
    public function onPasteButton(DataContainer $dc, array $row, string $table, bool $circularReference, $arrClipboard = false): string
    {
        $user = ($token = $this->tokenStorage->getToken()) ? $token->getUser() : null;

        if ($table === $GLOBALS['TL_DCA'][$dc->table]['config']['ptable']
            && 'root' === $row['type']
            && $user instanceof BackendUser
            && ($user->isAdmin || $user->isAllowed(5, $row))
            && !$circularReference
        ) {
            $theme = $this->getTheme((int) $row['layout']);

            $sections = array_diff(
                StringUtil::deserialize($theme['rootcontent'], true),
                $this->getExistingSections((int) $row['pid'], $dc->id)
            );

            if (!empty($sections)) {
                return '<a href="'.Backend::addToUrl('act='.$arrClipboard['mode'].'&amp;mode=2&amp;pid='.$row['id'].(!is_array($arrClipboard['id']) ? '&amp;id='.$arrClipboard['id'] : '')).'" title="'.StringUtil::specialchars(sprintf($GLOBALS['TL_LANG'][$dc->table]['pasteinto'][1], $row['id'])).'" onclick="Backend.getScrollOffset();">'.Backend::generateImage('pasteinto.gif', sprintf($GLOBALS['TL_LANG'][$dc->table]['pasteinto'][1], $row['id']), 'class="blink"').'</a> ';
            }

            return Backend::generateImage('pasteinto_.gif', '', 'class="blink"').' ';
        }

        if ($table == $dc->table) {
            $page = $this->getPage((int) $row['pid']);

            if ('root' === $page['type']) {
                return Backend::generateImage('pasteafter_.gif', '', 'class="blink"').' ';
            }
        }

        return (new \tl_article())->pasteArticle($dc, $row, $table, $circularReference, $arrClipboard);
    }

    private function getPage($articleId): array
    {
        $qb = $this->database->createQueryBuilder();

        $qb
            ->select('tl_page.*')
            ->from('tl_article')
            ->join('tl_article', 'tl_page', 'tl_article.pid=tl_page.id')
            ->where('tl_article.id = :articleId')
            ->setParameter('articleId', $articleId)
        ;

        return $qb->execute()->fetch();
    }

    private function getTheme(int $layoutId): array
    {
        $qb = $this->database->createQueryBuilder();

        $qb
            ->select('tl_theme.*')
            ->from('tl_theme')
            ->join('tl_layout', 'tl_layout', 'tl_theme.id=tl_layout.pid')
            ->where('tl_layout.id = :layout_id')
            ->setParameter('layout_id', $layoutId)
        ;

        return $qb->execute()->fetch();
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

        return $qb->execute()->fetchAll(\PDO::FETCH_COLUMN, 'title');
    }
}
