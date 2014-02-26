<?php

/**
 * Request controller
 *
 * @author Illuminator
 */
class RequestController extends BaseController {
	private $_facebookHelper;
	private $_facebook;

	public function __construct() {
		$this->_facebookHelper = FacebookHelper::getInstance();
		$this->_facebook = $this->_facebookHelper->getFacebook();
	}

	public function requests($subjectId) {
		if(Session::has("uid")) {
			$result = array();
			$requests = RequestL::where("subject_id", "=", $subjectId)->orderBy("created_at", "desc")->get();
			foreach($requests as $request) {
				$data = $this->_facebook->api(array(
					"method" => "fql.query",
					"query" => "SELECT name, profile_url FROM user WHERE uid = " . $request->author_id
				));
				if(count($data) == 1) {
					$author = $data[0]["name"];
					$authorUrl = $data[0]["profile_url"];
				} else {
					$author = "";
					$authorUrl = "";
				}
				$result[] = array(
					"id" => $request->id,
					"title" => $request->title,
					"message" => $request->message,
					"author" => $author,
					"author_url" => $authorUrl,
					"created_at" => $request->created_at,
					"updated_at" => $request->updated_at
				);
			}
			return Response::json($result);
		} else {
			return Response::json(array());
		}
	}

	public function newRequest($year, $category, $subjectId) {
		if(Session::has("uid")) {
			$isCompleted = Input::has("newRequestTitle") && Input::has("newRequestMessage");
			if($isCompleted) {
				$title = Input::get("newRequestTitle");
				$message = Input::get("newRequestMessage");
				$request = new RequestL();
				$request->title = HTML::entities($title);
				$request->message = HTML::entities($message);
				$request->author_id = Session::get("uid");
				$request->subject_id = $subjectId;
				$request->save();
				return Response::json(array(
					"success" => true
				));
			}
		} else {
			return Response::json(array(
				"success" => false
			));
		}
	}
}
