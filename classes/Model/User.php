<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * User Model, implements the Search trait.
 *
 * @package    MG/Search
 * @category   Model
 * @author     Modular Gaming
 * @copyright  (c) 2012-2014 Modular Gaming
 * @license    BSD http://www.modulargaming.com/license
 */
class Model_User extends MG_Model_User {
	use Model_ElasticSearch {
		update as _traitUpdate;
	}

	/**
	 * @return \Elastica\Document
	 */
	public function get_search_document() {
		return new \Elastica\Document($this->id, array(
			'id'       => $this->id,
			'username' => $this->username
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
			'id'       => array('type' => 'integer', 'include_in_all' => FALSE),
			'username' => array('type' => 'string', 'include_in_all' => TRUE),
		));

		// Send mapping to type
		$mapping->send();
	}

	/**
	 * Ensure we only update if the username was changed, to prevent useless queries at login (last_login).
	 *
	 * @param Validation $validation
	 * @return $this|ORM
	 */
	public function update(Validation $validation = NULL)
	{
		if ($this->changed('username'))
		{
			$this->_traitUpdate($validation);
		}
		else
		{
			parent::update($validation);
		}

		return $this;
	}
}