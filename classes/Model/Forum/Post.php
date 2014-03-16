<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Forum Post Model, implements the Search trait.
 *
 * @package    MG/Search
 * @category   Model
 * @author     Modular Gaming
 * @copyright  (c) 2012-2014 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Model_Forum_Post extends MG_Model_Forum_Post {
	use Model_ElasticSearch;

	/**
	 * @return \Elastica\Document
	 */
	public function get_search_document() {
		return new \Elastica\Document($this->id, array(
			'id'      => $this->id,
			'content' => $this->content
		));
	}

	/**
	 * @return \Elastica\Type\Mapping
	 */
	public function send_search_mapping()
	{
		$type = ElasticSearch::instance()->get_type($this->_search_type());

		$mapping = new \Elastica\Type\Mapping();
		$mapping->setType($type);

		// Set mapping
		$mapping->setProperties(array(
			'id'      => array('type' => 'integer', 'include_in_all' => FALSE),
			'content' => array('type' => 'string', 'include_in_all' => TRUE),
		));

		// Send mapping to type
		$mapping->send();
	}
}
