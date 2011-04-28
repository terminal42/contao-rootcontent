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
 * Operations
 */
$GLOBALS['TL_DCA']['tl_page']['list']['operations']['articles']['button_callback'][0] = 'tl_page_rootcontent';


class tl_page_rootcontent extends tl_page
{
	
	/**
	 * Generate an "edit articles" button and return it as string
	 * @param array
	 * @param string
	 * @param string
	 * @param string
	 * @param string
	 * @return string
	 */
	public function editArticles($row, $href, $label, $title, $icon)
	{
		if ($row['type'] == 'root')
		{
			return '<a href="' . $this->addToUrl($href.'&amp;node='.$row['id']) . '" title="'.specialchars($title).'">'.$this->generateImage($icon, $label).'</a> ';
		}
		
		return parent::editArticles($row, $href, $label, $title, $icon);
	}
}

