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



class ModuleRootContent extends Module
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_rootcontent';
	
	/**
	 * Root Content Database Result
	 */
	protected $objContent;
	
	
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ROOT CONTENT ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = $this->Environment->script.'?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}
		
		return parent::generate();
	}
	
	
	/**
	 * Abstract in parent...
	 */
	protected function compile()
	{
		global $objPage;
		$time = time();
		
		$objArticle = $this->Database->prepare("SELECT * FROM tl_article WHERE pid=? AND title=?" . (BE_USER_LOGGED_IN ? '' : " AND published='1' AND (start='' OR start<?) AND (stop='' OR stop>?)"))->limit(1)->execute($objPage->rootId, $this->rootcontent, $time, $time);
		
		if (!$objArticle->numRows)
		{
			return '';
		}
		
		$objArticle = new ModuleArticle($objArticle, 'main');
		$this->Template->article = $objArticle->generate(true);
	}
}

