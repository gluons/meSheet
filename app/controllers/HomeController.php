<?php

/**
 * Description of HomeController
 *
 * @author Illuminator
 */
class HomeController extends BaseController {
	private $_facebookHelper;
	private $_facebook;
	private $_loginUrl;

	public function __construct() {
		$this->_facebookHelper = FacebookHelper::getInstance();
		$this->_facebook = $this->_facebookHelper->getFacebook();
		$this->_loginUrl = $this->_facebook->getLoginUrl(array(
			"scope" => "email,user_groups",
			"display" => "page",
			"redirect_uri" => url("/newuser")
		));
	}

	public function newUser() {
		if($this->_facebookHelper->isLoggedIn()) {
			$me = $this->_facebook->api("/me");
			if($this->_facebookHelper->isEligible()) {
				if(User::where("id", "=", $me["id"])->count() == 0) {
					$user = new User();
					$user->id = $me["id"];
					$user->save();
				}
				return Redirect::to(URL::previous());
			} else {
				return Redirect::to("/forbidden");
			}
		} else {
			return Redirect::to(URL::previous());
		}
	}

	public function forbidden() {
		return View::make("forbidden");
	}

	public function index() {
		if($this->_facebookHelper->isLoggedIn()) {
			$me = $this->_facebook->api("/me");
			if(User::where("id", "=", $me["id"])->count() == 0) {
				return Redirect::to("/newuser");
			}
		}
		return View::make("index", array(
			"facebook" => $this->_facebook,
			"loginUrl" => $this->_loginUrl
		));
	}

	public function categories($year) {
		if($this->_facebookHelper->isLoggedIn()) {
			$me = $this->_facebook->api("/me");
			if(User::where("id", "=", $me["id"])->count() == 0) {
				return Redirect::to("/newuser");
			}
		}
		return View::make("categories", array(
			"facebook" => $this->_facebook,
			"loginUrl" => $this->_loginUrl,
			"year" => $year
		));
	}

	public function subjects($year, $category) {
		if($this->_facebookHelper->isLoggedIn()) {
			$me = $this->_facebook->api("/me");
			if(User::where("id", "=", $me["id"])->count() == 0) {
				return Redirect::to("/newuser");
			}
		}
		return View::make("subjects", array(
			"facebook" => $this->_facebook,
			"loginUrl" => $this->_loginUrl,
			"year" => $year,
			"category" => $category
		));
	}

}
