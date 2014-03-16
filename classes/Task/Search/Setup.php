<?php
/**
 * Task to setup mg index with snowball filter.
 *
 * @package    MG/User
 * @category   Task
 * @author     Modular Gaming
 * @copyright  (c) 2012-2013 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Task_Search_Setup extends Minion_Task {


	protected function _execute(array $params)
	{
		$index = ElasticSearch::instance()->get_index('mg');
		$index->create(
			array(
				'number_of_shards' => 4,
				'number_of_replicas' => 1,
				'analysis' => array(
					'analyzer' => array(
						'indexAnalyzer' => array(
							'type' => 'custom',
							'tokenizer' => 'standard',
							'filter' => array('lowercase', 'mySnowball')
						),
						'searchAnalyzer' => array(
							'type' => 'custom',
							'tokenizer' => 'standard',
							'filter' => array('standard', 'lowercase', 'mySnowball')
						)
					),
					'filter' => array(
						'mySnowball' => array(
							'type' => 'snowball',
							'language' => 'English'
						)
					)
				)
			),
			true
		);

		Minion_CLI::write('Search index setup complete!');
	}
}