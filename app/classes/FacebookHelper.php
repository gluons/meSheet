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

	public static function getLinkStat($url) {
		try {
			$query = <<<FQL
SELECT url, normalized_url, total_count, like_count, comment_count, share_count, click_count FROM link_stat WHERE url="$url"
FQL;
			$call = "https://graph.facebook.com/fql?q=" . rawurlencode($query);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $call);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);
			$output = json_decode($output);
			$output = $output->data;
			curl_close($ch);
			return $output;
		} catch(Exception $e) {
			return null;
		}
	}
	
	public static function getLikeCount($url) {
		$linkStat = FacebookHelper::getLinkStat($url);
		if($linkStat != null) {
			return $linkStat->like_count;
		} else {
			return 0;
		}
	}

	public function isEligible() {
		try {
			$isEligible = false;
			$groups = $this->_facebook->api("/me/groups")['data'];
			foreach ($groups as $group) {
				if ($group['id'] == "162895923753285") {
					$isEligible = true;
					break;
				}
			}
			return $isEligible;
		} catch(FacebookApiException $e) {
			return false;
		}
	}
}
