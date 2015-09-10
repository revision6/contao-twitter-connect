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
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['twitter_connect_api_key']     = array(
	'API key',
	'Please enter the key of your twitter app.'
);
$GLOBALS['TL_LANG']['tl_module']['twitter_connect_api_secret']  = array(
	'API secret',
	'Please enter the secret of your twitter app.'
);
$GLOBALS['TL_LANG']['tl_module']['twitter_connect_access_type'] = array(
	'Access type',
	'Please choose the access type.'
);
$GLOBALS['TL_LANG']['tl_module']['twitter_connect_groups']      = array(
	'Groups',
	'Please chose the groups, new members will be assigned too.'
);
$GLOBALS['TL_LANG']['tl_module']['twitter_connect_jumpTo']      = array(
	'Redirect page',
	'Please choose the page to which visitors will be redirected after successful log-in.'
);

$GLOBALS['TL_LANG']['tl_module']['twitter_activation_required']      = array(
	'Send activation mail',
	'User is disabled by default. He has to use the activation mail first.'
);

/**
 * References
 */
$GLOBALS['TL_LANG']['tl_module']['twitter_connect_access_types']['read']  = 'Read';
$GLOBALS['TL_LANG']['tl_module']['twitter_connect_access_types']['write'] = 'Read and Write';
