<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;

class ModuleSectionsListener
{
    private RequestStack $requestStack;
    private Connection $database;

    public function __construct(RequestStack $requestStack, Connection $database)
    {
        $this->requestStack = $requestStack;
        $this->database = $database;
    }

    /**
     * @Callback(table="tl_module", target="fields.rootcontent.options")
     */
    public function onOptionsCallback(DataContainer $dc): array
    {
        $qb = $this->database->createQueryBuilder();
        $qb
            ->select('rootcontent')
            ->from('tl_theme')
            ->where('id = :id')
            ->setParameter('id', $this->getThemeId($dc))
        ;

        return StringUtil::deserialize($qb->execute()->fetchOne(), true);
    }

    private function getThemeId(DataContainer $dc): int
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request && 'overrideAll' === $request->query->get('act')) {
            return $request->query->getInt('id');
        }

        return (int) $dc->activeRecord->pid;
    }
}
