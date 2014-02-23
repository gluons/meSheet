<?php

/**
 * Description of FileController
 *
 * @author Illuminator
 */
class FileController extends BaseController {
	private $_facebookHelper;
	private $_facebook;
	
	public function __construct() {
		$this->_facebookHelper = FacebookHelper::getInstance();
		$this->_facebook = $this->_facebookHelper->getFacebook();
	}

	public function topics($subjectId) {
		if(Session::has("uid")) {
			$result = array();
			$topics = Topic::where("subject_id", "=", $subjectId)->orderBy("created_at", "desc");
			foreach($topics as $topic) {
				$data = $this->_facebook->api(array(
					"method" => "fql.query",
					"query" => "SELECT name, profile_url FROM user WHERE uid = " . $topic->author_id
				));
				if(count($data) == 1) {
					$author = $data[0]["name"];
					$authorUrl = $data[0]["profile_url"];
				} else {
					$author = "";
					$authorUrl = "";
				}
				$result[] = array(
					"id" => $topic->id,
					"title" => $topic->title,
					"description" => $topic->description,
					"filename" => $topic->filename,
					"filetype" => $topic->filetype,
					"author" => $author,
					"author_url" => $authorUrl,
					"created_at" => $topic->created_at,
					"updated_at" => $topic->updated_at,
				);
			}
			$response = Response::make(json_encode($result));
			$response->header("Content-Type", "application/json");
			return $response;
		} else {
			$response = Response::make(json_encode(array()));
			$response->header("Content-Type", "application/json");
			return $response;
		}
	}

	public function download($year, $category, $subjectId, $topicId) {
		$uploadDir = Config::get("mesheet.upload_dir");
		if(Session::has("uid")) {
			$me = $this->_facebook->api("/me");
			$topic = Topic::where("id", "=", $topicId);
			if($topic->count() == 1) {
				$topic = $topic->first();
				$filepath = $uploadDir . "/" . strtolower($year) . "/" . strtolower($category) . "/" . $subjectId . "/" . $topic->id . "-" . $topic->filename;
				return Response::download($filepath);
			} else {
				return Response::make("File not found.", 404);
			}
		} else {
			return Redirect::to("/");
		}
	}
}
