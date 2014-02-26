<?php

/**
 * Facebook helper
 *
 * @author Illuminator
 */
class FacebookHelper {
	private static $instance = null;
	private $_config = array();
	private $_facebook = null;
	
	/**
	 * Initialize Facebook configuration.
	 */
	private function __construct() {
		$this->_config = array(
			"appId" => Config::get("facebook.id"),
			"secret" => Config::get("facebook.secret"),
			"allowSignedRequest" => false,
			"cookie" => true
		);
		$this->_facebook = new Facebook($this->_config);
	}

	/**
	 * Get a instance of Facebook helper.
	 * 
	 * @return FacebookHelper
	 */
	public static function getInstance() {
		if(FacebookHelper::$instance == null) {
			FacebookHelper::$instance = new FacebookHelper();
		}
		return FacebookHelper::$instance;
	}
	
	/**
	 * Get Facebook configuration.
	 * 
	 * @return array
	 */
	public function getConfig() {
		return $this->_config;
	}

	/**
	 * Get Facebook instance.
	 * 
	 * @return Facebook
	 */
	public function getFacebook() {
		return $this->_facebook;
	}

	/**
	 * Get Facebook stat of url.
	 * 
	 * @param String $url
	 * @return object
	 */
	public static function getLinkStat($url) {
		try {
			$query = <<<FQL
SELECT url, normalized_url, total_count, like_count, comment_count, share_count, click_count FROM link_stat WHERE url="$url"
FQL;
			$call = "https://graph.facebook.com/fql?q=" . rawurlencode($query);
			$output = file_get_contents($call);
			$output = json_decode($output);
			$output = $output->data[0];
			return $output;
		} catch(Exception $e) {
			return null;
		}
	}

	/**
	 * Get total count of Facebook stat for a url.
	 * 
	 * @param string $url
	 * @return int
	 */
	public static function getTotalCount($url) {
		$linkStat = FacebookHelper::getLinkStat($url);
		if($linkStat != null) {
			return $linkStat->total_count;
		} else {
			return 0;
		}
	}

	/**
	 * Get like count of Facebook stat for a url.
	 * 
	 * @param string $url
	 * @return int
	 */
	public static function getLikeCount($url) {
		$linkStat = FacebookHelper::getLinkStat($url);
		if($linkStat != null) {
			return $linkStat->like_count;
		} else {
			return 0;
		}
	}

	/**
	 * Check user is in IT KMITL group or not.
	 * 
	 * @return boolean
	 */
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
