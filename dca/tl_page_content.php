<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Andreas Schempp 2011
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */


/**
 * Table tl_page_content
 */
$GLOBALS['TL_DCA']['tl_page_content'] = array
(

	// Config
	'config' => array
	(
		'dataContainer'					=> 'Table',
		'enableVersioning'				=> true,
		'ptable'						=> 'tl_page',
		'onload_callback'				=> array
		(
			array('tl_page_content', 'initDCA'),
		),
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'						=> 4,
			'fields'					=> array('section'),
			'flag'						=> 1,
			'panelLayout'				=> 'search,limit',
			'headerFields'				=> array('title', 'dns', 'language'),
			'child_record_callback'		=> array('tl_page_content', 'listRows'),
			'disableGrouping'			=> true,
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'					=> 'act=select',
				'class'					=> 'header_edit_all',
				'attributes'			=> 'onclick="Backend.getScrollOffset();"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_page_content']['edit'],
				'href'					=> 'act=edit',
				'icon'					=> 'edit.gif'
			),
			'copy' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_page_content']['copy'],
				'href'					=> 'act=copy',
				'icon'					=> 'copy.gif'
			),
			'delete' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_page_content']['delete'],
				'href'					=> 'act=delete',
				'icon'					=> 'delete.gif',
				'attributes'			=> 'onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"'
			),
			'show' => array
			(
				'label'					=> &$GLOBALS['TL_LANG']['tl_page_content']['show'],
				'href'					=> 'act=show',
				'icon'					=> 'show.gif'
			),
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'						=> '{section_legend},section;{text_legend},text',
	),

	// Fields
	'fields' => array
	(
		'section' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_page_content']['section'],
			'inputType'					=> 'select',
			'eval'						=> array('mandatory'=>true, 'includeBlankOption'=>true, 'tl_class'=>'clr'),
		),
		'text' => array
		(
			'label'						=> &$GLOBALS['TL_LANG']['tl_page_content']['text'],
			'exclude'					=> true,
			'search'					=> true,
			'inputType'					=> 'textarea',
			'eval'						=> array('mandatory'=>true, 'rte'=>'tinyMCE', 'helpwizard'=>true),
			'explanation'				=> 'insertTags'
		),
	)
);


class tl_page_content extends Backend
{

	/**
	 * Add an image to each record
	 * @param array
	 * @param string
	 * @return string
	 */
	public function listRows($row)
	{
		return '
<div class="cte_type"><strong>' . $row['section'] . '</strong></div>
<div class="limit_height' . (!$GLOBALS['TL_CONFIG']['doNotCollapse'] ? ' h64' : '') . ' block">
' . $row['text'] . '
</div>' . "\n";
	}
	
	
	/**
	 * 
	 */
	public function initDCA($dc)
	{
		if ($this->Input->get('act') == '' || $this->Input->get('act') == 'overrideAll')
		{
			$objPage = $this->Database->execute("SELECT * FROM tl_page WHERE id=".$dc->id);
		}
		else
		{
			$objPage = $this->Database->execute("SELECT tl_page.* FROM tl_page_content LEFT JOIN tl_page ON tl_page_content.pid=tl_page.id WHERE tl_page_content.id=".$dc->id);
		}

		// Root page must have a page layout assigned
		if (!$objPage->numRows || !$objPage->includeLayout || !$objPage->layout)
			return array();
		
		$objTheme = $this->Database->execute("SELECT tl_theme.* FROM tl_theme LEFT JOIN tl_layout ON tl_theme.id=tl_layout.pid WHERE tl_layout.id=".$objPage->layout);
		
		// Huh? Theme not found??
		if (!$objTheme->numRows)
			return array();
		
		$objSections = $this->Database->execute("SELECT * FROM tl_page_content WHERE pid=" . $objPage->id . " AND id!=" . $dc->id);
		$arrSections = deserialize($objTheme->rootcontent, true);
		
		$arrSections = array_diff($arrSections, $objSections->fetchEach('section'));

		if (!count($arrSections))
		{
			$GLOBALS['TL_DCA'][$dc->table]['config']['closed'] = true;
		}

		if ($this->Input->get('act') == 'edit')
		{
			$GLOBALS['TL_DCA'][$dc->table]['fields']['section']['options'] = $arrSections;
		}
	}
}

