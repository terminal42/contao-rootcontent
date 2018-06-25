<?php

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['config']['onload_callback'][] = ['terminal42_rootcontent.listener.module_field', 'onLoadCallback'];
$GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'defineRootLimit';
$GLOBALS['TL_DCA']['tl_module']['palettes']['rootcontent'] = '{title_legend},name,type;{include_legend},rootcontent;{protected_legend:hide},protected;{expert_legend:hide},guests';
$GLOBALS['TL_DCA']['tl_module']['subpalettes']['defineRootLimit'] = 'rootLimit';

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

$GLOBALS['TL_DCA']['tl_module']['fields']['defineRootLimit'] = [
    'label'             => &$GLOBALS['TL_LANG']['tl_module']['defineRootLimit'],
    'inputType'         => 'checkbox',
    'eval'              => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql'               => ['type' => 'string', 'length' => 1, 'default' => '', 'options' => ['fixed' => true]],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['rootLimit'] = [
    'label'             => &$GLOBALS['TL_LANG']['tl_module']['rootLimit'],
    'inputType'         => 'checkbox',
    'options_callback'  => [Terminal42\RootcontentBundle\EventListener\RootLimitListener::class, 'onRootLimitOptions'],
    'eval'              => ['multiple' => true, 'tl_class' => 'clr'],
    'sql'               => ['type' => \Doctrine\DBAL\Types\Type::TEXT, 'length' => \Doctrine\DBAL\Platforms\MySqlPlatform::LENGTH_LIMIT_TEXT, 'notnull' => false],
];
