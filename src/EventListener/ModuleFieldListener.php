<?php

declare(strict_types=1);

namespace Terminal42\RootcontentBundle\EventListener;

use Contao\CoreBundle\DataContainer\PaletteManipulator;
use Contao\CoreBundle\ServiceAnnotation\Callback;

/**
 * @Callback(table="tl_module", target="config.onload")
 */
class ModuleFieldListener
{
    public function __invoke(): void
    {
        $pm = PaletteManipulator::create()->addField(
            'defineRootLimit',
            'protected_legend',
            PaletteManipulator::POSITION_APPEND
        );

        foreach ($GLOBALS['TL_DCA']['tl_module']['palettes'] as $name => $palette) {
            if ('__selector__' !== $name && 'default' !== $name) {
                $pm->applyToPalette($name, 'tl_module');
            }
        }
    }
}
