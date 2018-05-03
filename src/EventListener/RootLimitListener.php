<?php

declare(strict_types=1);

/*
 * rootcontent extension for Contao Open Source CMS
 *
 * @copyright  Copyright (c) 2018, terminal42 gmbh
 * @author     terminal42 gmbh <info@terminal42.ch>
 * @license    LGPL-3.0-or-later
 * @link       http://github.com/terminal42/contao-asset-reload
 */

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\Model;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;

class RootLimitListener
{
    /**
     * @var Connection
     */
    private $database;

    /**
     * Constructor.
     *
     * @param Connection $database
     */
    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    /**
     * Gets the root pages.
     *
     * @return array
     */
    public function onRootLimitOptions()
    {
        $qb = $this->database->createQueryBuilder();

        $qb
            ->select('id, title, dns, language')
            ->from('tl_page')
            ->where("type='root'")
            ->orderBy('sorting', 'ASC')
        ;

        $pages = [];
        foreach ($qb->execute()->fetchAll() as $row) {
            $label = $row['title'];

            if ($row['dns']) {
                $label .= ' <span style="color:#999;padding-left:3px">'.$row['dns'].')</span>';
            }

            $pages[$row['id']] = $label;
        }

        return $pages;
    }

    /**
     * Checks if a frontend module has been limited for root pages.
     *
     * @param Model $model
     * @param bool  $isVisible
     *
     * @return bool
     */
    public function onIsVisibleElement(Model $model, bool $isVisible)
    {
        if ($model instanceof ModuleModel && $model->defineRootLimit) {
            global $objPage;
            $rootLimit = StringUtil::deserialize($model->rootLimit, true);

            if ($objPage instanceof PageModel && !\in_array($objPage->loadDetails()->rootId, $rootLimit, false)) {
                return false;
            }
        }

        return $isVisible;
    }
}
