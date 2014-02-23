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
	
	public function login() {
		if(!Session::has("uid")) {
			try {
				$data = $this->_facebook->api(array(
					"method" => "fql.query",
					"query" => "SELECT uid FROM user WHERE uid == me()"
				));
				if(count($data) == 1) {
					$userId = $data[0]["uid"];
					Session::put("uid", $userId);
				}
			} catch(FacebookApiException $e) {
			}
		}
	}

	public function logout() {
		Session::flush();
		if(Input::has("next")) {
			$next = Input::get("next");
		} else {
			$next = URL::previous();
		}
		$logoutUrl = "https://www.facebook.com/logout.php?next=" . $next . "&access_token=" . $this->_facebook->getAccessToken();
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
		$categories = Category::orderBy("name")->get();
		return View::make("years", array(
			"facebook" => $this->_facebook,
			"year" => $year,
			"categories" => $categories
		));
	}

	public function categories($year, $category) {
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category);
			}
		}
		$yearInt = 0;
		switch(strtolower($year)) {
			case "freshman":
				$yearInt = 1;
				break;
			case "sophomore":
				$yearInt = 2;
				break;
			case "junior":
				$yearInt = 3;
				break;
			case "senior":
				$yearInt = 4;
				break;
		}
		$categoryId = Category::where("name", "=", $category)->pluck("id");
		$subjects = Subject::where("year", "=", $yearInt)->where("category_id", "=", $categoryId)->get();
		return View::make("categories", array(
			"facebook" => $this->_facebook,
			"year" => $year,
			"category" => $category,
			"subjects" => $subjects
		));
	}

	public function subjects($year, $category, $subjectId) {
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category . "/" . $subjectId);
			}
		}
		$subject = Subject::where("id", "=", $subjectId)->pluck("name");
		return View::make("subjects", array(
			"facebook" => $this->_facebook,
			"year" => $year,
			"category" => $category,
			"subjectId" => $subjectId,
			"subject" => $subject
		));
	}
	
	public function subjects2($year, $category, $subjectId) {
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category . "/" . $subjectId);
			}
		}
		$subject = Subject::where("id", "=", $subjectId)->pluck("name");
		return View::make("subjects", array(
			"facebook" => $this->_facebook,
			"year" => $year,
			"category" => $category,
			"subjectId" => $subjectId,
			"subject" => $subject,
			"isRequest" => true
		));
	}

	public function topics($year, $category, $subjectId, $topicId) {
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category . "/" . $subjectId . "/topic/" . $topicId);
			}
		}
		$subject = Subject::where("id", "=", $subjectId)->pluck("name");
		return View::make("subjects", array(
			"facebook" => $this->_facebook,
			"year" => $year,
			"category" => $category,
			"subjectId" => $subjectId,
			"subject" => $subject,
			"topicId" => $topicId
		));
	}

	public function requests($year, $category, $subjectId, $requestId) {
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category . "/" . $subjectId . "/request/" . $requestId);
			}
		}
		$subject = Subject::where("id", "=", $subjectId)->pluck("name");
		return View::make("subjects", array(
			"facebook" => $this->_facebook,
			"year" => $year,
			"category" => $category,
			"subjectId" => $subjectId,
			"subject" => $subject,
			"requestId" => $requestId
		));
	}

}
