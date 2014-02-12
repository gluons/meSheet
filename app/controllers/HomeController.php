<?php

/**
 * Description of HomeController
 *
 * @author Illuminator
 */
class HomeController extends BaseController {

	public function index() {
		return View::make('index');
	}

	public function categories($year) {
		return View::make('categories', array(
			'year' => $year
		));
	}

	public function subjects($year, $category) {
		return View::make('subjects', array(
			'year' => $year,
			'category' => $category
		));
	}
}
