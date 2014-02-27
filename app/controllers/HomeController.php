<?php

/**
 * Home controller
 *
 * @author Illuminator
 */
class HomeController extends BaseController {
	private $_facebookHelper;
	private $_facebook;

	public function __construct() {
		$this->_facebookHelper = FacebookHelper::getInstance();
		$this->_facebook = $this->_facebookHelper->getFacebook();
	}

	/**
	 * Controller for login.
	 */
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

	/**
	 * Controller for logout.
	 */
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

	/**
	 * Controller for new user.
	 */
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

	/**
	 * Controller for forbinden page.
	 */
	public function forbidden() {
		return View::make("forbidden");
	}

	/**
	 * Controller for home page.
	 */
	public function index() {
		$isRoot = false;
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/");
			}
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$isRoot = true;
					break;
				default:
					$isRoot = false;
					break;
			}
		}
		return View::make("index", array(
			"facebook" => $this->_facebook,
			"isRoot" => $isRoot
		));
	}

	/**
	 * Controller for years page.
	 * 
	 * @param string $year
	 */
	public function years($year) {
		$isRoot = false;
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year);
			}
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$isRoot = true;
					break;
				default:
					$isRoot = false;
					break;
			}
		}
		$categories = Category::orderBy("name")->get();
		return View::make("years", array(
			"facebook" => $this->_facebook,
			"isRoot" => $isRoot,
			"year" => $year,
			"categories" => $categories
		));
	}

	/**
	 * Controller for categories page.
	 * 
	 * @param string $year
	 * @param string $category
	 */
	public function categories($year, $category) {
		$isRoot = false;
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category);
			}
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$isRoot = true;
					break;
				default:
					$isRoot = false;
					break;
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
			"isRoot" => $isRoot,
			"year" => $year,
			"category" => $category,
			"subjects" => $subjects
		));
	}

	/**
	 * Controller for subjects page that show file list.
	 * 
	 * @param string $year
	 * @param string $category
	 * @param string $subjectId
	 */
	public function subjects($year, $category, $subjectId) {
		$isRoot = false;
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category . "/" . $subjectId);
			}
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$isRoot = true;
					break;
				default:
					$isRoot = false;
					break;
			}
		}
		$errorMsg = null;
		if(Session::has("error_message")) {
			$errorMsg = Session::get("error_message");
			Session::forget("error_message");
		}
		$subject = Subject::where("id", "=", $subjectId)->pluck("name");
		return View::make("subjects", array(
			"facebook" => $this->_facebook,
			"isRoot" => $isRoot,
			"year" => $year,
			"category" => $category,
			"subjectId" => $subjectId,
			"subject" => $subject,
			"errorMsg" => $errorMsg
		));
	}

	/**
	 * Controller for subjects page that show request list.
	 * 
	 * @param string $year
	 * @param string $category
	 * @param string $subjectId
	 */
	public function subjects2($year, $category, $subjectId) {
		$isRoot = false;
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category . "/" . $subjectId);
			}
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$isRoot = true;
					break;
				default:
					$isRoot = false;
					break;
			}
		}
		$errorMsg = null;
		if(Session::has("error_message")) {
			$errorMsg = Session::get("error_message");
			Session::forget("error_message");
		}
		$subject = Subject::where("id", "=", $subjectId)->pluck("name");
		return View::make("subjects", array(
			"facebook" => $this->_facebook,
			"isRoot" => $isRoot,
			"year" => $year,
			"category" => $category,
			"subjectId" => $subjectId,
			"subject" => $subject,
			"isRequest" => true,
			"errorMsg" => $errorMsg
		));
	}

	/**
	 * Controller for topic view page.
	 * 
	 * @param string $year
	 * @param string $category
	 * @param string $subjectId
	 * @param string $topicId
	 */
	public function topics($year, $category, $subjectId, $topicId) {
		$isRoot = false;
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category . "/" . $subjectId . "/topic/" . $topicId);
			}
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$isRoot = true;
					break;
				default:
					$isRoot = false;
					break;
			}
		}
		$errorMsg = null;
		if(Session::has("error_message")) {
			$errorMsg = Session::get("error_message");
			Session::forget("error_message");
		}
		$subject = Subject::where("id", "=", $subjectId)->pluck("name");
		return View::make("subjects", array(
			"facebook" => $this->_facebook,
			"isRoot" => $isRoot,
			"year" => $year,
			"category" => $category,
			"subjectId" => $subjectId,
			"subject" => $subject,
			"topicId" => $topicId,
			"errorMsg" => $errorMsg
		));
	}

	/**
	 * Controller for request view page.
	 * 
	 * @param string $year
	 * @param string $category
	 * @param string $subjectId
	 * @param string $requestId
	 */
	public function requests($year, $category, $subjectId, $requestId) {
		$isRoot = false;
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category . "/" . $subjectId . "/request/" . $requestId);
			}
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$isRoot = true;
					break;
				default:
					$isRoot = false;
					break;
			}
		}
		$errorMsg = null;
		if(Session::has("error_message")) {
			$errorMsg = Session::get("error_message");
			Session::forget("error_message");
		}
		$subject = Subject::where("id", "=", $subjectId)->pluck("name");
		return View::make("subjects", array(
			"facebook" => $this->_facebook,
			"isRoot" => $isRoot,
			"year" => $year,
			"category" => $category,
			"subjectId" => $subjectId,
			"subject" => $subject,
			"requestId" => $requestId,
			"errorMsg" => $errorMsg
		));
	}

}
