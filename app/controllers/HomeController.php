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
	}
	
	public function logout() {
		Session::flush();
		$logoutUrl = "https://www.facebook.com/logout.php?next=" . URL::previous() . "&access_token=" . $this->_facebook->getAccessToken();
		$this->_facebook->destroySession();
		return Redirect::to($logoutUrl);
	}

	public function newUser() {
		$from = Session::get("from");
		try {
			$data = $this->_facebook->api(array(
				"method" => "fql.query",
				"query" => "SELECT uid FROM user WHERE uid == me()"
			));
			if(count($data) == 1) {
				$userId = $data[0]["uid"];
				Session::put("uid", $userId);
				if($this->_facebookHelper->isEligible()) {
					if(User::where("id", "=", $userId)->count() == 0) {
						$user = new User();
						$user->id = $userId;
						$user->save();
					}
					return Redirect::to($from);
				} else {
					return Redirect::to("/forbidden");
				}
			} else {
				Session::flush();
				return Redirect::to($from);
			}
		} catch(FacebookApiException $e) {
			Session::flush();
			return Redirect::to($from);
		}
	}

	public function forbidden() {
		return View::make("forbidden");
	}

	public function index() {
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/");
			}
		}
		return View::make("index", array(
			"facebook" => $this->_facebook
		));
	}

	public function years($year) {
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year);
			}
		}
		return View::make("years", array(
			"facebook" => $this->_facebook,
			"year" => $year
		));
	}

	public function categories($year, $category) {
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category);
			}
		}
		return View::make("categories", array(
			"facebook" => $this->_facebook,
			"year" => $year,
			"category" => $category
		));
	}

	public function subjects($year, $category, $subject) {
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category . "/" . $subject);
			}
		}
		return View::make("subjects", array(
			"facebook" => $this->_facebook,
			"year" => $year,
			"category" => $category,
			"subject" => $subject
		));
	}

}
