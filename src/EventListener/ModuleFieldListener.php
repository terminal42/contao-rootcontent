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

class ModuleFieldListener
{
    public function onLoadCallback(): void
    {
        $pm = \Contao\CoreBundle\DataContainer\PaletteManipulator::create()->addField(
            'defineRootLimit',
            'protected_legend',
            \Contao\CoreBundle\DataContainer\PaletteManipulator::POSITION_APPEND
        );

        foreach ($GLOBALS['TL_DCA']['tl_module']['palettes'] as $name => $palette) {
            if ('__selector__' !== $name && 'default' !== $name) {
                $pm->applyToPalette($name, 'tl_module');
            }
        }
    }
}
