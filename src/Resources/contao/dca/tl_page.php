<?php

use Contao\CoreBundle\DataContainer\PaletteManipulator;

/**
 * Operations
 */
$GLOBALS['TL_DCA']['tl_page']['list']['operations']['articles']['button_callback'] = [
    'terminal42_rootcontent.listener.article_operation',
    'onButtonCallback',
];

PaletteManipulator::create()
    ->addLegend('expert_legend', 'protected', PaletteManipulator::POSITION_BEFORE)
    ->addField('cssClass', 'expert')
    ->applyToPalette('root', 'tl_page')
;
