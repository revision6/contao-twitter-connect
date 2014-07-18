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

namespace Bit3\Contao\TwitterConnect\Event;

use Symfony\Component\EventDispatcher\Event;

class PreConnectEvent extends Event
{
	/**
	 * @var string
	 */
	protected $token;

	/**
	 * @var string
	 */
	protected $verifier;

	public function __construct($token, $verifier)
	{
		$this->token    = (string) $token;
		$this->verifier = (string) $verifier;
	}

	/**
	 * @return string
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * @param string $token
	 */
	public function setToken($token)
	{
		$this->token = (string) $token;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getVerifier()
	{
		return $this->verifier;
	}

	/**
	 * @param string $verifier
	 */
	public function setVerifier($verifier)
	{
		$this->verifier = (string) $verifier;
		return $this;
	}
}