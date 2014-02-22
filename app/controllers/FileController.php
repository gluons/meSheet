<?php

/**
 * Description of FileController
 *
 * @author Illuminator
 */
class FileController extends BaseController {
	public function download($year, $category, $subjectId, $topicId) {
		$uploadDir = Config::get("mesheet.upload_dir");
		$topic = Topic::where("id", "=", $topicId)->first();
		$filepath = $uploadDir . "/" . strtolower($year) . "/" . strtolower($category) . "/" . $subjectId . "/" . $topic->filename;
		return Response::download($filepath);
	}
}
