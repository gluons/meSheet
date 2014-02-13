<?php

/**
 * Description of FacebookHelper
 *
 * @author Illuminator
 */
class FacebookHelper {
	private static $instance = null;
	private $_config = array();
	private $_facebook = null;
	
	private function __construct() {
		$this->_config = array(
			"appId" => Config::get("facebook.id"),
			"secret" => Config::get("facebook.secret"),
			"allowSignedRequest" => false,
			"cookie" => true
		);
		$this->_facebook = new Facebook($this->_config);
	}

	public static function getInstance() {
		if(FacebookHelper::$instance == null) {
			FacebookHelper::$instance = new FacebookHelper();
		}
		return FacebookHelper::$instance;
	}
	
	public function getConfig() {
		return $this->_config;
	}

	public function getFacebook() {
		return $this->_facebook;
	}

	public function isLoggedIn() {
		$isLoggedIn = false;
		try {
			$me = $this->_facebook->api("/me");
			$isLoggedIn = true;
		} catch (FacebookApiException $e) {
			$isLoggedIn = false;
		}
		return $isLoggedIn;
	}

	public function isEligible() {
		$isLoggedIn = $this->isLoggedIn();
		$isEligible = false;
		if ($isLoggedIn) {
			$groups = $this->_facebook->api("/me/groups")['data'];
			foreach ($groups as $group) {
				if ($group['id'] == "162895923753285") {
					$isEligible = true;
					break;
				}
			}
		}
		return $isLoggedIn && $isEligible;
	}
}
