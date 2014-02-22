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
		$subjects = Subject::where("category_id", "=", $categoryId)->where("year", "=", $yearInt)->get();
		return View::make("categories", array(
			"facebook" => $this->_facebook,
			"year" => $year,
			"category" => $category,
			"subjects" => $subjects
		));
	}

	public function subjects($year, $category, $subjectId) {
		$uploadDir = Config::get("mesheet.upload_dir");
		if(Session::has("uid")) {
			$userId = Session::get("uid");
			if(User::where("id", "=", $userId)->count() == 0) {
				return Redirect::to("/newuser")->with("from", "/" . $year . "/" . $category . "/" . $subject);
			}
		}
		$subject = Subject::where("id", "=", $subjectId)->pluck("name");
		$topicList = Topic::where("subject_id", "=", $subjectId)->get();
		$fileList = array();
		$requestList = RequestL::where("subject_id", "=", $subjectId)->orderBy("created_at", "desc");
		foreach($topicList as $topic) {
			$file = new stdClass();
			$file->id = $topic->id;
			$file->title = $topic->title;
			$file->description = $topic->description;
			$file->filename = $topic->filename;
			$file->filesize = @filesize($uploadDir . "/" . strtolower($year) . "/" . strtolower($category) . "/" . $subjectId . "/" . $topic->filename);
			$sz = 'BKMGTP';
			$factor = floor((strlen($file->filesize) - 1) / 3);
			$file->filesize = sprintf("%.2f", $file->filesize / pow(1024, $factor)) . " " . @$sz[$factor];
			$file->filetype = $topic->filetype;
			$file->created_at = $topic->created_at;
			$file->updated_at = $topic->updated_at;
			$file->author_id = $topic->author_id;
			$file->url = url("/" . strtolower($year) . "/" . strtolower($category) . "/" . $subjectId . "/" . $file->id);
			$file->like_count = FacebookHelper::getLikeCount($file->url);
			$file->filepath = url("/download/" . strtolower($year) . "/" . strtolower($category) . "/" . $subjectId . "/" . $file->id);
			array_push($fileList, $file);
		}
		return View::make("subjects", array(
			"facebook" => $this->_facebook,
			"year" => $year,
			"category" => $category,
			"subjectId" => $subjectId,
			"subject" => $subject,
			"uploadDir" => $uploadDir,
			"fileList" => $fileList,
			"requestList" => $requestList,
		));
	}

}
