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
 * Callbacks
 */
$GLOBALS['TL_DCA']['tl_article']['config']['onload_callback'][] = array('tl_article_rootcontent', 'prepareContentSections');
$GLOBALS['TL_DCA']['tl_article']['list']['sorting']['paste_button_callback'][0] = 'tl_article_rootcontent';


class tl_article_rootcontent extends tl_article
{

	/**
	 * Overrides parent function, allow paste for root pages
	 */
	public function pasteArticle(DataContainer $dc, $row, $table, $cr, $arrClipboard=false)
	{
		if ($table == $GLOBALS['TL_DCA'][$dc->table]['config']['ptable'] && $row['type'] == 'root' && ($this->User->isAdmin || $this->User->isAllowed(5, $row)) && !$cr)
		{
			$objTheme = $this->Database->execute("SELECT tl_theme.* FROM tl_theme LEFT JOIN tl_layout ON tl_theme.id=tl_layout.pid WHERE tl_layout.id=".$row['layout']);
				
			$objSections = $this->Database->execute("SELECT * FROM tl_article WHERE pid=" . $row['id']);
			$arrSections = deserialize($objTheme->rootcontent, true);
			
			$arrSections = array_diff($arrSections, $objSections->fetchEach('title'));
			
			if (count($arrSections))
			{
				return '<a href="'.$this->addToUrl('act='.$arrClipboard['mode'].'&amp;mode=2&amp;pid='.$row['id'].(!is_array($arrClipboard['id']) ? '&amp;id='.$arrClipboard['id'] : '')).'" title="'.specialchars(sprintf($GLOBALS['TL_LANG'][$dc->table]['pasteinto'][1], $row['id'])).'" onclick="Backend.getScrollOffset();">'.$this->generateImage('pasteinto.gif', sprintf($GLOBALS['TL_LANG'][$dc->table]['pasteinto'][1], $row['id']), 'class="blink"').'</a> ';
			}
			else
			{
				return $this->generateImage('pasteinto_.gif', '', 'class="blink"').' ';
			}
		}
		elseif ($table == $dc->table)
		{
			$objPage = $this->Database->prepare("SELECT * FROM tl_page WHERE id=?")
								  ->limit(1)
								  ->execute($row['pid']);
			
			if ($objPage->type == 'root')
			{
				return $this->generateImage('pasteafter_.gif', '', 'class="blink"').' ';
			}
		}
		
		return parent::pasteArticle($dc, $row, $table, $cr, $arrClipboard);
	}
	
	
	/**
	 * If the article is in a root page, we show a select menu instead of article name
	 */
	public function prepareContentSections($dc)
	{
		if ($this->Input->get('act') == 'edit')
		{
			$objPage = $this->Database->execute("SELECT tl_page.* FROM tl_article LEFT JOIN tl_page ON tl_article.pid=tl_page.id WHERE tl_article.id={$dc->id}");
			
			if ($objPage->type == 'root')
			{
				$GLOBALS['TL_DCA']['tl_article']['palettes']['default'] = '{title_legend},title,author;{expert_legend:hide},cssID;{publish_legend},published';
				
				$objTheme = $this->Database->execute("SELECT tl_theme.* FROM tl_theme LEFT JOIN tl_layout ON tl_theme.id=tl_layout.pid WHERE tl_layout.id=".$objPage->layout);
				
				$objSections = $this->Database->execute("SELECT * FROM tl_article WHERE pid=" . $objPage->id . " AND id!=" . $dc->id);
				$arrSections = deserialize($objTheme->rootcontent, true);
				
				$arrSections = array_diff($arrSections, $objSections->fetchEach('title'));

				$GLOBALS['TL_DCA']['tl_article']['fields']['title'] = array
				(
					'label'		=> &$GLOBALS['TL_LANG']['tl_article']['rootcontent'],
					'inputType'	=> 'select',
					'options'	=> array_values($arrSections),
					'eval'		=> array('mandatory'=>true, 'includeBlankOption'=>true),
				);
			}
		}
	}
}

