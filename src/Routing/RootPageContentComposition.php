<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\Routing;

use Contao\CoreBundle\Routing\Page\ContentCompositionInterface;
use Contao\PageModel;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;

class RootPageContentComposition implements ContentCompositionInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function supportsContentComposition(PageModel $pageModel): bool
    {
        if ('root' !== $pageModel->type) {
            return false;
        }

        $pageModel->loadDetails();

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('rootcontent')
            ->from('tl_theme')
            ->leftJoin('tl_theme', 'tl_layout', 'tl_layout', 'tl_theme.id=tl_layout.pid')
            ->where('tl_layout.id = :layout_id')
            ->setParameter('layout_id', $pageModel->layout)
        ;

        $definedSections = StringUtil::deserialize($qb->execute()->fetchOne(), true);

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('title')
            ->from('tl_article')
            ->where('pid = :page_id')
            ->setParameter('page_id', $pageModel->id)
        ;

        $usedSections = $qb->execute()->fetchFirstColumn();

        // Always show the "articles" button in the page tree
        if (($request = $this->requestStack->getMainRequest()) && 'page' === $request->get('do')) {
            return !empty($definedSections);
        }

        return !empty($definedSections) && \count(array_diff(
            $definedSections,
            $usedSections,
        )) > 0;
    }
}
