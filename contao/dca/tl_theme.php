<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;

PaletteManipulator::create()
    ->addLegend('rootcontent_legend', '', PaletteManipulator::POSITION_APPEND)
    ->addField('rootcontent', 'rootcontent_legend', PaletteManipulator::POSITION_APPEND)
    ->applyToPalette('default', 'tl_theme')
;

$GLOBALS['TL_DCA']['tl_theme']['fields']['rootcontent'] = [
    'label' => &$GLOBALS['TL_LANG']['tl_theme']['rootcontent'],
    'inputType' => 'listWizard',
    'sql' => ['type' => 'text', 'notnull' => false, 'length' => 65535],
];
