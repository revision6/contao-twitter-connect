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

namespace Bit3\Contao\TwitterConnect;

use Bit3\Contao\TwitterConnect\Event\FaultyAuthenticateEvent;
use Bit3\Contao\TwitterConnect\Event\FaultyConnectEvent;
use Bit3\Contao\TwitterConnect\Event\InitConnectEvent;
use Bit3\Contao\TwitterConnect\Event\PostAuthenticateEvent;
use Bit3\Contao\TwitterConnect\Event\PostConnectEvent;
use Bit3\Contao\TwitterConnect\Event\PreAuthenticateEvent;
use Bit3\Contao\TwitterConnect\Event\PreConnectEvent;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Message\EntityEnclosingRequest;
use Guzzle\Plugin\Oauth\OauthPlugin;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * The Twitter Connect module / content element that handle the whole connect and authentication process.
 */
class TwitterConnect extends \TwigSimpleHybrid
{

	protected $strTemplate = 'mod_twitter_connect';

	/**
	 * {@inheritdoc}
	 */
	protected function compile()
	{
		if (TL_MODE == 'BE') {
			return;
		}

		if (empty($this->twitter_connect_api_key)) {
			throw new \RuntimeException('No API KEY is defined!');
		}
		if (empty($this->twitter_connect_api_secret)) {
			throw new \RuntimeException('No API SECRET is defined!');
		}

		// Form submit -> initiate connect
		if (\Input::post('TL_FORM') == 'twitter_connect_' . $this->id) {
			$this->init();
		}

		// state is valid and code is provided -> connect success
		else if (
			\Input::get('oauth_token') &&
			\Input::get('oauth_verifier') &&
			isset($_SESSION['TWITTER_CONNECT_PARAMS']) &&
			\Input::get('oauth_token') == $_SESSION['TWITTER_CONNECT_PARAMS']['oauth_token']
		) {
			$this->connect(\Input::get('oauth_token'), \Input::get('oauth_verifier'));
		}

		// Twitter login success, now authenticate the member
		else if (isset($_SESSION['TWITTER_CONNECT_LOGIN'])) {
			list($username, $tokenSecret) = $_SESSION['TWITTER_CONNECT_LOGIN'];
			unset($_SESSION['TWITTER_CONNECT_LOGIN']);

			$this->authenticateUser($username, $tokenSecret);
		}
	}

	/**
	 * Initiate the twitter connect.
	 */
	protected function init()
	{
		$redirectUrl = \Environment::get('base') .
			\Controller::generateFrontendUrl($GLOBALS['objPage']->row());

		try {
			$url = 'https://api.twitter.com/oauth/request_token?' . http_build_query(
					array(
						'x_auth_access_type' => $this->twitter_connect_access_type
					)
				);

			$oauth = new OauthPlugin(
				array(
					'callback'        => $redirectUrl,
					'consumer_key'    => $this->twitter_connect_api_key,
					'consumer_secret' => $this->twitter_connect_api_secret,
				)
			);

			$client = new Client();
			$client->addSubscriber($oauth);

			$request  = $client->post($url, array('oauth_callback' => $redirectUrl));
			$response = $request->send();

			$body   = $response->getBody(true);
			$params = array();
			parse_str($body, $params);

			$url = 'https://api.twitter.com/oauth/authenticate?' . http_build_query(
					array(
						'oauth_token' => $params['oauth_token']
					)
				);

			/** @var EventDispatcherInterface $eventDispatcher */
			$eventDispatcher = $GLOBALS['container']['event-dispatcher'];

			$event = new InitConnectEvent($url, $params);
			$eventDispatcher->dispatch(TwitterConnectEvents::INIT_CONNECT, $event);

			$_SESSION['TWITTER_CONNECT_PARAMS'] = $event->getParams();
			$url                                = $event->getUrl();

			\Controller::redirect($url);
		}
		catch (BadResponseException $exception) {
			$redirectUrl .= '?' . http_build_query(
					array(
						'status_code'    => $exception->getResponse()->getStatusCode(),
						'status_message' => $exception->getResponse()->getReasonPhrase(),
					)
				);
			\Controller::redirect($redirectUrl);
		}
	}

	/**
	 * Process the connect against twitter.
	 *
	 * @param string $code
	 *
	 * @throws \Exception
	 */
	protected function connect($token, $verifier)
	{
		/** @var EventDispatcherInterface $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];

		$redirectUrl = \Environment::get('base') .
			\Controller::generateFrontendUrl($GLOBALS['objPage']->row());

		$event = new PreConnectEvent($token, $verifier);
		$eventDispatcher->dispatch(TwitterConnectEvents::PRE_CONNECT, $event);
		$token    = $event->getToken();
		$verifier = $event->getVerifier();

		try {
			// receive a new access token
			$oauth = new OauthPlugin(
				array(
					'consumer_key'    => $this->twitter_connect_api_key,
					'consumer_secret' => $this->twitter_connect_api_secret,
					'token'           => $token,
				)
			);

			$client = new Client();
			$client->addSubscriber($oauth);

			/** @var EntityEnclosingRequest $request */
			$request = $client->post('https://api.twitter.com/oauth/access_token');
			$request->setBody(
				http_build_query(
					array(
						'oauth_verifier' => $verifier
					)
				),
				'application/x-www-form-urlencoded'
			);

			$response = $request->send();

			parse_str($response->getBody(true), $params);

			// fetch user profile
			$oauth = new OauthPlugin(
				array(
					'consumer_key'    => $this->twitter_connect_api_key,
					'consumer_secret' => $this->twitter_connect_api_secret,
					'token'           => $params['oauth_token'],
					'token_secret'    => $params['oauth_token_secret'],
				)
			);

			$client = new Client();
			$client->addSubscriber($oauth);

			$url = 'https://api.twitter.com/1.1/account/verify_credentials.json';

			$request  = $client->get($url);
			$response = $request->send();

			$userData = json_decode($response->getBody(true), true);

			// faulty connect
			if (empty($userData['id'])) {
				$event = new FaultyConnectEvent($params, $userData);
				$eventDispatcher->dispatch(TwitterConnectEvents::FAULTY_CONNECT, $event);

				\Controller::redirect(\Environment::get('base'));
			}

			$member    = \MemberModel::findOneBy('twitter_id', $userData['id']);
			$newMember = false;

			if (!$member) {
				$newMember = true;

				// generate username
				$username = $userData['screen_name'];
				for ($n = 1; \MemberModel::findBy('username', $username); $n++) {
					$username = $userData['screen_name'] . '-' . $n;
				}

				$name = explode(' ', $userData['name'], 2);

				$member             = new \MemberModel();
				$member->tstamp     = time();
				$member->groups     = deserialize($this->twitter_connect_groups, true);
				$member->dateAdded  = time();
				$member->createdOn  = time();
				$member->firstname  = $name[0];
				$member->lastname   = count($name) > 1 ? $name[1] : '';
				$member->login      = 1;
				$member->username   = $username;
				$member->language   = $userData['lang'];
				$member->twitter_id = $userData['id'];
			}

			$member->password             = \Encryption::hash($params['oauth_token_secret']);
			$member->twitter_link         = sprintf('https://twitter.com/%s', $userData['screen_name']);
			$member->twitter_token        = $params['oauth_token'];
			$member->twitter_token_secret = $params['oauth_token_secret'];

			$event = new PostConnectEvent($params, $userData, $member, $newMember);
			$eventDispatcher->dispatch(TwitterConnectEvents::POST_CONNECT, $event);

			$event->getMember()->save();

			unset($_SESSION['TWITTER_CONNECT_PARAMS']);
			$_SESSION['TWITTER_CONNECT_LOGIN'] = array($member->username, $params['oauth_token_secret']);

			\Controller::redirect($redirectUrl);
		}
		catch (BadResponseException $exception) {
			$redirectUrl .= '?' . http_build_query(
					array(
						'status_code'    => $exception->getResponse()->getStatusCode(),
						'status_message' => $exception->getResponse()->getReasonPhrase(),
					)
				);
			\Controller::redirect($redirectUrl);
		}
	}

	/**
	 * Authenticate the user after successfully connect to twitter.
	 *
	 * @param string $username
	 * @param string $accessToken
	 * @param string $referer
	 *
	 * @throws \RuntimeException
	 */
	protected function authenticateUser($username, $tokenSecret)
	{
		/** @var EventDispatcherInterface $eventDispatcher */
		$eventDispatcher = $GLOBALS['container']['event-dispatcher'];

		$frontendUser = \FrontendUser::getInstance();

		$event = new PreAuthenticateEvent($frontendUser, $username, $tokenSecret);
		$eventDispatcher->dispatch(TwitterConnectEvents::PRE_AUTHENTICATE, $event);

		$frontendUser = $event->getFrontendUser();
		$username     = $event->getUsername();
		$tokenSecret  = $event->getTokenSecret();

		// set credentials
		\Input::setPost('username', $username);
		\Input::setPost('password', $tokenSecret);

		if ($frontendUser->login()) {
			$event = new PostAuthenticateEvent($frontendUser, $username, $tokenSecret);
			$eventDispatcher->dispatch(TwitterConnectEvents::POST_AUTHENTICATE, $event);

			// redirect to jump to page
			if ($this->twitter_connect_jumpTo) {
				$page = \PageModel::findByPk($this->twitter_connect_jumpTo);

				if (!$page) {
					throw new \RuntimeException('Page ID ' . $this->twitter_connect_jumpTo . ' was not found');
				}

				\Controller::redirect($page->row());
			}

			// redirect to start page
			\Controller::redirect(\Environment::get('base'));
		}
		else {
			$event = new FaultyAuthenticateEvent($frontendUser, $username, $tokenSecret);
			$eventDispatcher->dispatch(TwitterConnectEvents::FAULTY_AUTHENTICATE, $event);

			// redirect to start page
			\Controller::redirect(\Environment::get('base'));
		}
	}
}
