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
$GLOBALS['TL_DCA']['tl_module']['metapalettes']['twitter_connect'] = array(
	'type'             => array('name', 'headline', 'type'),
	'twitter_connect' => array(
		'twitter_connect_api_key',
		'twitter_connect_api_secret',
		'twitter_connect_access_type',
		'twitter_activation_required',
		'twitter_connect_groups',
		'twitter_connect_jumpTo'
	),
	'protected'        => array(':hide', 'protected'),
	'expert'           => array(':hide', 'guests', 'cssID', 'space'),
);

$GLOBALS['TL_DCA']['tl_module']['metasubpalettes']['twitter_activation_required'] = array(
	'nc_notification'
);

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_module']['fields']['twitter_connect_api_key']     = array(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['twitter_connect_api_key'],
	'inputType' => 'text',
	'eval'      => array(
		'mandatory' => true,
		'tl_class'  => 'w50',
		'maxlength' => 255,
		'doNotCopy' => true,
		'doNotShow' => true,
	),
	'sql'       => 'varchar(255) NOT NULL default \'\'',
);
$GLOBALS['TL_DCA']['tl_module']['fields']['twitter_connect_api_secret'] = array(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['twitter_connect_api_secret'],
	'inputType' => 'text',
	'eval'      => array(
		'mandatory' => true,
		'tl_class'  => 'w50',
		'maxlength' => 255,
		'doNotCopy' => true,
		'doNotShow' => true,
	),
	'sql'       => 'varchar(255) NOT NULL default \'\'',
);
$GLOBALS['TL_DCA']['tl_module']['fields']['twitter_connect_access_type'] = array(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['twitter_connect_access_type'],
	'inputType' => 'select',
	'options'   => array('read', 'write'),
	'reference' => &$GLOBALS['TL_LANG']['tl_module']['twitter_connect_access_types'],
	'eval'      => array(
		'mandatory' => true,
		'tl_class'  => 'clr',
	),
	'sql'       => 'char(5) NOT NULL default \'\''
);
$GLOBALS['TL_DCA']['tl_module']['fields']['twitter_connect_groups']     = array(
	'label'      => &$GLOBALS['TL_LANG']['tl_module']['twitter_connect_groups'],
	'inputType'  => 'checkbox',
	'foreignKey' => 'tl_member_group.name',
	'eval'       => array(
		'mandatory' => true,
		'multiple'  => true,
		'tl_class'  => 'clr',
	),
	'sql'        => 'varchar(255) NOT NULL default \'\''
);
$GLOBALS['TL_DCA']['tl_module']['fields']['twitter_connect_jumpTo']     = array(
	'label'     => &$GLOBALS['TL_LANG']['tl_module']['twitter_connect_jumpTo'],
	'inputType' => 'pageTree',
	'eval'      => array(
		'tl_class' => 'clr',
	),
	'sql'       => 'int(10) NOT NULL default \'0\'',
);
$GLOBALS['TL_DCA']['tl_module']['fields']['twitter_activation_required']     = array(
	'label'      => &$GLOBALS['TL_LANG']['tl_module']['twitter_activation_required'],
	'foreignKey' => 'tl_member_group.name',
	'eval'       => array(
		'multiple'  => false,
		'tl_class'  => 'clr',
		'submitOnChange' => true,
	),
	'sql'        => 'char(255) NOT NULL default \'\''
);
