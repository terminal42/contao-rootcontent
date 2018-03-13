<?php

$GLOBALS['TL_DCA']['tl_article']['config']['onload_callback'][] = array('terminal42_rootcontent.listener.article_section', 'onLoad');
$GLOBALS['TL_DCA']['tl_article']['list']['sorting']['paste_button_callback'] = ['terminal42_rootcontent.listener.article_section', 'onPasteButton'];
