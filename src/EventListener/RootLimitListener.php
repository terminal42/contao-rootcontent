<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\Model;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Doctrine\DBAL\Connection;

class RootLimitListener
{
    private Connection $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    /**
     * Gets the root pages.
     *
     * @Callback(table="tl_module", target="fields.rootLimit.options")
     */
    public function onRootLimitOptions(): array
    {
        $qb = $this->database->createQueryBuilder();

        $qb
            ->select('id, title, dns, language')
            ->from('tl_page')
            ->where("type='root'")
            ->orderBy('sorting', 'ASC')
        ;

        $pages = [];
        foreach ($qb->execute()->fetchAllAssociative() as $row) {
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
     * @Hook("isVisibleElement")
     */
    public function onIsVisibleElement(Model $model, bool $isVisible): bool
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
