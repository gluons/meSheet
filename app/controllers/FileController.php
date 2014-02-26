<?php

/**
 * File controller
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

	/**
	 * Controller for getting all topic of a subject as JSON.
	 * 
	 * @param string $subjectId
	 */
	public function topics($subjectId) {
		$uploadDir = Config::get("mesheet.upload_dir");
		if(Session::has("uid")) {
			$result = array();
			$topics = Topic::where("subject_id", "=", $subjectId)->orderBy("created_at", "desc")->get();
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
				$years = array("freshman", "sophomore", "junior", "senior");
				$year = Subject::where("id", "=", $subjectId)->pluck("year");
				$year = $years[$year - 1];
				$category = DB::table("category")->join("subject", "category.id", "=", "subject.category_id")->where("subject.id", "=", $subjectId)->pluck("category.name");
				$filepath = $uploadDir . "/" . strtolower($year) . "/" . strtolower($category) . "/" . $subjectId . "/" . $topic->id . "-" . $topic->filename;
				$filesize = @filesize($filepath);
				if($filesize !== false) {
					$sz = 'BKMGTP';
					$factor = floor((strlen($filesize) - 1) / 3);
					$filesize = sprintf("%.2f", $filesize / pow(1024, $factor)) . " " . @$sz[$factor];
				}
				$result[] = array(
					"id" => $topic->id,
					"title" => $topic->title,
					"description" => $topic->description,
					"filename" => $topic->filename,
					"filetype" => $topic->filetype,
					"filesize" => $filesize,
					"author" => $author,
					"author_url" => $authorUrl,
					"created_at" => $topic->created_at,
					"updated_at" => $topic->updated_at,
				);
			}
			return Response::json($result);
		} else {
			return Response::json(array());
		}
	}

	/**
	 * Add new file
	 * 
	 * @param string $year
	 * @param string $category
	 * @param string $subjectId
	 */
	public function newTopic($year, $category, $subjectId) {
		$uploadDir = Config::get("mesheet.upload_dir");
		$allowedExt = array("pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt");
		$allowedTypes = array("/image\/.+/");
		if(Session::has("uid")) {
			$isCompleted = Input::has("newFileTitle") && Input::has("newFileDescription") && Input::hasFile("newFileFile");
			if($isCompleted) {
				$filepath = $uploadDir . "/" . strtolower($year) . "/" . strtolower($category) . "/" . $subjectId . "/";
				if(!file_exists($uploadDir . "/" . strtolower($year) . "/")) {
					mkdir($uploadDir . "/" . strtolower($year) . "/");
				}
				if(!file_exists($uploadDir . "/" . strtolower($year) . "/" . strtolower($category) . "/")) {
					mkdir($uploadDir . "/" . strtolower($year) . "/" . strtolower($category) . "/");
				}
				if(!file_exists($filepath)) {
					mkdir($filepath);
				}
				$title = Input::get("newFileTitle");
				$description = Input::get("newFileDescription");
				$file = Input::file("newFileFile");
				$isValidFile = false;
				if(in_array($file->getClientOriginalExtension(), $allowedExt)) {
					$isValidFile = true;
				}
				foreach($allowedTypes as $allowedType) {
					if(preg_match($allowedType, $file->getMimeType())) {
						$isValidFile = true;
						break;
					}
				}
				
				if($isValidFile) {
					$topic = new Topic();
					$topic->title = HTML::entities($title);
					$topic->description = HTML::entities($description);
					$topic->filename = $file->getClientOriginalName();
					$topic->filetype = $file->getMimeType();
					$topic->author_id = Session::get("uid");
					$topic->subject_id = $subjectId;
					$topic->save();
					$file->move($filepath, $topic->id . "-" . $topic->filename);
					return Redirect::to("/" . $year . "/" . $category . "/" . $subjectId);
				} else {
					return Redirect::to("/" . $year . "/" . $category . "/" . $subjectId)->with("error_message", "Invalid file.");
				}
			}
		} else {
			return Redirect::to("/");
		}
	}

	/**
	 * Download file
	 * 
	 * @param string $year
	 * @param string $category
	 * @param string $subjectId
	 * @param string $topicId
	 */
	public function download($year, $category, $subjectId, $topicId) {
		$uploadDir = Config::get("mesheet.upload_dir");
		if(Session::has("uid")) {
			$topic = Topic::where("id", "=", $topicId);
			if($topic->count() == 1) {
				$topic = $topic->first();
				$filepath = $uploadDir . "/" . strtolower($year) . "/" . strtolower($category) . "/" . $subjectId . "/" . $topic->id . "-" . $topic->filename;
				return Response::download($filepath, $topic->filename);
			} else {
				return Response::make("File not found.", 404);
			}
		} else {
			return Redirect::to("/");
		}
	}
}
