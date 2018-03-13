<?php

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['rootcontent'] = '{title_legend},name,type;{include_legend},rootcontent;{protected_legend:hide},protected;{expert_legend:hide},guests';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['rootcontent'] = [
    'label'             => &$GLOBALS['TL_LANG']['tl_module']['rootcontent'],
    'inputType'         => 'select',
    'options_callback'  => ['terminal42_rootcontent.listener.module_sections', 'onOptionsCallback'],
    'eval'              => ['mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql'               => ['type' => 'string', 'default' => ''],
];
