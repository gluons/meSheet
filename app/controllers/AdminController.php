<?php
/**
 * Admin controller
 *
 * @author Illuminator
 */
class AdminController extends BaseController {
	private $_facebookHelper;
	private $_facebook;

	public function __construct() {
		$this->_facebookHelper = FacebookHelper::getInstance();
		$this->_facebook = $this->_facebookHelper->getFacebook();
	}

	public function categories() {
		if(Session::has("uid")) {
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					return View::make("adm.categories", array(
						"facebook" => $this->_facebook
					));
				default:
					return Redirect::to("/");
			}
		} else {
			return Redirect::to("/");
		}
	}

	public function categoryList() {
		if(Session::has("uid")) {
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$result = array();
					$categories = Category::orderBy("id")->get();
					foreach($categories as $category) {
						$result[] = array(
							"id" => $category->id,
							"name" => $category->name
						);
					}
					return Response::json($result);
				default:
					return Response::json(array());
			}
		} else {
			return Response::json(array());
		}
	}

	public function categoryUpdate() {
		if(Session::has("uid")) {
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$isCompleted = Input::has("name");
					if(Input::has("id")) {
						$isCompleted = $isCompleted && true;
						$category = Category::where("id", "=", Input::get("id"))->first();
					} else {
						$category = new Category();
					}
					if($isCompleted) {
						$nameCount = Category::where("name", "=", Input::get("name"));
						if(Input::has("id")) {
							$nameCount = $nameCount->where("id", "!=", Input::get("id"));
						}
						$nameCount = $nameCount->count();
						if($nameCount > 0) {
							return Response::json(array(
								"success" => false,
								"error_message" => "Duplicate name"
							));
						}
						$category->name = HTML::entities(Input::get("name"));
						$category->save();
					}
					return Response::json(array(
						"success" => true
					));
				default:
					return Response::json(array(
						"success" => false,
						"error_message" => "No permission"
					));
			}
		} else {
			return Response::json(array(
				"success" => false,
				"error_message" => "Not login"
			));
		}
	}

	public function categoryRemove() {
		if(Session::has("uid")) {
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$isCompleted = Input::has("id");
					if($isCompleted) {
						$category = Category::find(Input::get("id"));
						$category->delete();
					}
					return Response::json(array(
						"success" => true
					));
				default:
					return Response::json(array(
						"success" => false,
						"error_message" => "No permission"
					));
			}
		} else {
			return Response::json(array(
				"success" => false,
				"error_message" => "Not login"
			));
		}
	}

	public function subjects() {
		if(Session::has("uid")) {
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					return View::make("adm.subjects", array(
						"facebook" => $this->_facebook
					));
				default:
					return Redirect::to("/");
			}
		} else {
			return Redirect::to("/");
		}
	}

	public function subjectsList() {
		if(Session::has("uid")) {
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$isCompleted = Input::has("year") && Input::has("category_id");
					if($isCompleted) {
						$year = Input::get("year");
						$categoryId = Input::get("category_id");
						$result = array();
						$subjects = Subject::where("year", "=", $year)->where("category_id", "=", $categoryId)->get();
						foreach($subjects as $subject) {
							$result[] = array(
								"id" => $subject->id,
								"name" => $subject->name
							);
						}
						return Response::json($result);
					} else {
						Response::json(array());
					}
				default:
					return Response::json(array());
			}
		} else {
			return Response::json(array());
		}
	}

	public function subjectsUpdate() {
		if(Session::has("uid")) {
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$isCompleted = Input::has("name");
					if(Input::has("id")) {
						$isCompleted = $isCompleted && true;
						$subject = Subject::where("id", "=", Input::get("id"))->first();
					} else {
						$isCompleted = $isCompleted && Input::has("year") && Input::has("category_id");
						$subject = new Subject();
					}
					if($isCompleted) {
						$subject->name = HTML::entities(Input::get("name"));
						if(!Input::has("id")) {
							$subject->year = Input::get("year");
							$subject->category_id = Input::get("category_id");
						}
						$subject->save();
					}
					return Response::json(array(
						"success" => true
					));
				default:
					return Response::json(array(
						"success" => false,
						"error_message" => "No permission"
					));
			}
		} else {
			return Response::json(array(
				"success" => false,
				"error_message" => "Not login"
			));
		}
	}

	public function subjectsRemove() {
		if(Session::has("uid")) {
			$role = User::where("id", "=", Session::get("uid"))->pluck("role");
			switch($role) {
				case "root":
					$isCompleted = Input::has("id");
					if($isCompleted) {
						$subject = Subject::find(Input::get("id"));
						$subject->delete();
					}
					return Response::json(array(
						"success" => true
					));
				default:
					return Response::json(array(
						"success" => false,
						"error_message" => "No permission"
					));
			}
		} else {
			return Response::json(array(
				"success" => false,
				"error_message" => "Not login"
			));
		}
	}
}
