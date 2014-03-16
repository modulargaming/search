<?php
/**
 * Task to mass update search index.
 *
 * Available config options are:
 *
 *  --model=User
 *
 *  This is the name of the model you wish to update.
 *
 *  --limit=100
 *
 *  This is how many models you wish to update at a time.
 *
 *  --times=-1
 *
 *  This is how many times the indexing should continue, -1 means until everything is indexed.
 *
 *  --offset=0
 *
 *  This is the offset you wish to begin at, in case some models are already indexed.
 *
 * @package    MG/Search
 * @category   Task
 * @author     Modular Gaming
 * @copyright  (c) 2012-2014 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Task_Search_Index extends Minion_Task {

	protected $_options = array(
		'model'  => 'User',
		'limit'  => 100,
		'times'  => -1,
		'offset' => 0
	);

	protected function _execute(array $params)
	{
		$model = $params['model'];
		$limit = $params['limit'];
		$times = $params['times'];
		$offset = $params['offset'];

		$elasticsearch = ElasticSearch::instance();

		$orm = ORM::factory($model);
		$type = $orm->_search_type();

		$total = $orm->count_all();

		// Write the header
		Minion_CLI::write('#########################################' . str_repeat('#', strlen($model))   . '##');
		Minion_CLI::write('# Bulk updating search indexes in model: ' . Minion_CLI::color($model, 'blue') . ' #');
		Minion_CLI::write('#########################################' . str_repeat('#', strlen($model))   . '##');
		Minion_CLI::write();
		Minion_CLI::write('Importing ' . $limit . ' items at a time, beginning at offset ' . $offset . ', ' . $times . ' times');
		Minion_CLI::write();

		while ($times !== 0)
		{
			$results = ORM::factory($model)
				->limit($limit)
				->offset($offset)
				->find_all();

			// Break the loop if there are no more results in the database.
			if ($results->count() <= 0)
			{
				break;
			}

			$documents = array();
			foreach ($results as $result)
			{
				$documents[] = $result->get_search_document();
			}

			$elasticsearch->add_documents($type, $documents);

			$current = $offset + $results->count();
			Minion_CLI::write('Imported: ' . $current . '/'.$total);

			$offset += $limit;
			$times -= 1;
		}

		// Write the footer
		Minion_CLI::write();
		Minion_CLI::write('##########################');
		Minion_CLI::write('# Bulk update completed! #');
		Minion_CLI::write('##########################');

	}
}