<?php

/**
 * Description of HomeController
 *
 * @author Illuminator
 */
class HomeController extends BaseController {

	protected $layout = 'layouts.master';

	public function index() {
		return View::make('index');
	}

	public function indexBody() {
		return View::make('body.index');
	}

	public function categories() {
		return View::make('categories');
	}

	public function categoriesBody() {
		return View::make('body.categories');
	}

}
