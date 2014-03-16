<?php
/**
 * Task to mass update search index.
 *
 * Available config options are:
 *
 *  --model=User
 *
 * @package    MG/Search
 * @category   Task
 * @author     Modular Gaming
 * @copyright  (c) 2012-2014 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Task_Search_Mapping extends Minion_Task {

	protected $_options = array(
		'model'  => 'User',
	);

	protected function _execute(array $params)
	{
		$model = $params['model'];
		$orm = ORM::factory($model);

		$orm->send_search_mapping();

		Minion_CLI::write('Search mapping for model ' . Minion_CLI::color($model, 'blue') . ' updated!');
	}
}