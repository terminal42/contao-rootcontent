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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_module']['palettes']['rootcontent'] = '{title_legend},name,type;{include_legend},rootcontent;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['rootcontent'] = array
(
	'label'			=> &$GLOBALS['TL_LANG']['tl_module']['rootcontent'],
	'inputType'		=> 'select',
	'options_callback'	=> array('tl_module_rootcontent', 'getSections'),
	'eval'				=> array('mandatory'=>true, 'includeBlankOption'=>true),
);


class tl_module_rootcontent extends Backend
{
	
	public function getSections($dc)
	{
		$intPid = $dc->activeRecord->pid;

		if ($this->Input->get('act') == 'overrideAll')
		{
			$intPid = (int)$this->Input->get('id');
		}
		
		$objTheme = $this->Database->execute("SELECT * FROM tl_theme WHERE id=" . $intPid);
		
		return deserialize($objTheme->rootcontent, true);
	}
}

