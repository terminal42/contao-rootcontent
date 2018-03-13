<?php

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_theme']['palettes']['default'] .= ';{rootcontent_legend},rootcontent';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_theme']['fields']['rootcontent'] = [
    'label'         => &$GLOBALS['TL_LANG']['tl_theme']['rootcontent'],
    'inputType'     => 'listWizard',
    'sql'           => ['type' => 'text'],
];
