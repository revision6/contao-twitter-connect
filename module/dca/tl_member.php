<?php

/**
 * This file is part of the Twitter Connect extension for Contao Open Source CMS.
 *
 * (c) 2014 Tristan Lins <tristan.lins@bit3.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    bit3/contao-twitter-connect
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @copyright  2014 Tristan Lins <tristan.lins@bit3.de>
 * @link       https://bit3.de
 * @license    MIT
 * @filesource
 */

/**
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_member']['palettes']['default'] .= ';{twitter_legend},twitter_id,twitter_link';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_member']['fields']['twitter_id']               = array(
	'label'     => &$GLOBALS['TL_LANG']['tl_member']['twitter_id'],
	'inputType' => 'text',
	'eval'      => array('tl_class' => 'w50', 'maxlength' => 255, 'doNotCopy' => true),
	'sql'       => 'varchar(255) NOT NULL default \'\''
);
$GLOBALS['TL_DCA']['tl_member']['fields']['twitter_link']             = array(
	'label'     => &$GLOBALS['TL_LANG']['tl_member']['twitter_link'],
	'inputType' => 'text',
	'eval'      => array('tl_class' => 'w50', 'maxlength' => 255, 'doNotCopy' => true),
	'sql'       => 'varchar(255) NOT NULL default \'\''
);
$GLOBALS['TL_DCA']['tl_member']['fields']['twitter_token']     = array(
	'label' => &$GLOBALS['TL_LANG']['tl_member']['twitter_token'],
	'eval'  => array('doNotCopy' => true, 'doNotShow' => true),
	'sql'   => 'varchar(255) NOT NULL default \'\''
);
$GLOBALS['TL_DCA']['tl_member']['fields']['twitter_token_secret'] = array(
	'label' => &$GLOBALS['TL_LANG']['tl_member']['twitter_token_secret'],
	'eval'  => array('doNotCopy' => true),
	'sql'   => 'int(10) NOT NULL default \'0\''
);
