<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;

#[AsCallback(table: 'tl_module', target: 'config.onload')]
class ModuleFieldListener
{
    public function __invoke(): void
    {
        $pm = PaletteManipulator::create()->addField(
            'defineRootLimit',
            'protected_legend',
            PaletteManipulator::POSITION_APPEND,
        );

        foreach (array_keys($GLOBALS['TL_DCA']['tl_module']['palettes']) as $name) {
            if ('__selector__' !== $name && 'default' !== $name) {
                $pm->applyToPalette($name, 'tl_module');
            }
        }
    }
}
