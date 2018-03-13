<?php

/**
 * Operations
 */
$GLOBALS['TL_DCA']['tl_page']['list']['operations']['articles']['button_callback'] = [
    'terminal42_rootcontent.listener.article_operation',
    'onButtonCallback',
];
