<?php
class MicController extends BaseController {

	public $layout='column2';

	protected function beforeAction($action) {
		if (!Yii::app()->user->checkAccess('admin')) {
			throw new CHttpException(403, 'You are not authorised to perform this action.');
		}
		return parent::beforeAction($action);
	}

	public function actionIndex() {
		$main_start = microtime(true);

		$results = array();

		// Find
		$start = microtime(true);
		for($i = 1; $i <= 100; $i++) {
			$disorders = Disorder::model()->findAll(array(
				'limit' => 100
			));
		}
		$results['Find 100 disorders 100 times'] = microtime(true) - $start;

		// Save
		$disorders = Disorder::model()->findAll(array(
			'limit' => 200
		));
		$count = count($disorders);
		$start = microtime(true);
		foreach($disorders as $disorder) {
			$disorder->save();
		}
		$results["Save $count disorders"] = microtime(true) - $start;

		// Disk write
		$start = microtime(true);
		for($i = 1; $i <= 1000; $i++) {
			$data = str_repeat(rand(0,9), 1000000);
			file_put_contents('/tmp/oe-perf-test-'.$i, $data);
			unlink('/tmp/oe-perf-test-'.$i);
		}
		$results["Write 1000 random 1MByte files"] = microtime(true) - $start;

		$this->render('index', array(
			'results' => $results,
		));
		Yii::log('Total Time: '. (microtime(true) - $main_start));
	}
}
