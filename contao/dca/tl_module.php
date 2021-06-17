<?php

use Doctrine\DBAL\Platforms\MySqlPlatform;
use Doctrine\DBAL\Types\Types;

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'defineRootLimit';
$GLOBALS['TL_DCA']['tl_module']['palettes']['rootcontent'] = '{title_legend},name,type;{include_legend},rootcontent;{protected_legend:hide},protected;{expert_legend:hide},guests';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['defineRootLimit'] = 'rootLimit';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['rootcontent'] = [
    'label'             => &$GLOBALS['TL_LANG']['tl_module']['rootcontent'],
    'inputType'         => 'select',
    'eval'              => ['mandatory' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql'               => ['type' => 'string', 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['defineRootLimit'] = [
    'label'             => &$GLOBALS['TL_LANG']['tl_module']['defineRootLimit'],
    'inputType'         => 'checkbox',
    'eval'              => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql'               => ['type' => 'string', 'length' => 1, 'default' => '', 'options' => ['fixed' => true]],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['rootLimit'] = [
    'label'             => &$GLOBALS['TL_LANG']['tl_module']['rootLimit'],
    'inputType'         => 'checkbox',
    'eval'              => ['multiple' => true, 'tl_class' => 'clr'],
    'sql'               => ['type' => Types::TEXT, 'length' => MySqlPlatform::LENGTH_LIMIT_TEXT, 'notnull' => false],
];
