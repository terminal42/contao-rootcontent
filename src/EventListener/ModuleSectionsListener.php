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

use Contao\DataContainer;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;

class ModuleSectionsListener
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
     * Constructor.
     *
     * @param RequestStack $requestStack
     * @param Connection   $database
     */
    public function __construct(RequestStack $requestStack, Connection $database)
    {
        $this->requestStack = $requestStack;
        $this->database = $database;
    }

    public function onOptionsCallback(DataContainer $dc): array
    {
        $qb = $this->database->createQueryBuilder();
        $qb
            ->select('rootcontent')
            ->from('tl_theme')
            ->where('id = :id')
            ->setParameter('id', $this->getThemeId($dc))
        ;

        return StringUtil::deserialize($qb->execute()->fetchColumn(), true);
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
