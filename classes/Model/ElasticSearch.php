<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Class Model_ElasticSearch
 */
trait Model_ElasticSearch {

	/** @var bool Search type, used for indexing */
	protected $_search_type;

	/** @var bool Enable search indexing */
	protected $_search_enabled = TRUE;

	/**
	 * @return \Elastica\Document
	 */
	public abstract function get_search_document();

	/**
	 * @return \Elastica\Type\Mapping
	 */
	public abstract function send_search_mapping();

	/**
	 * Return _search type if available otherwise default to table name.
	 *
	 * @return string
	 */
	public function _search_type()
	{
		if ($this->_search_type === NULL)
		{
			return $this->_table_name;
		}
	}

	/**
	 * Insert a new object to the database
	 * @param  Validation $validation Validation object
	 * @throws Kohana_Exception
	 * @return ORM
	 */
	public function create(Validation $validation = NULL)
	{
		parent::create($validation);

		if ($this->_search_enabled)
		{
			// Add the document to the search server.
			ElasticSearch::instance()->add_document($this->_search_type(), $this->get_search_document());
		}


		return $this;
	}

	/**
	 * Updates a single record or multiple records
	 *
	 * @chainable
	 * @param  Validation $validation Validation object
	 * @throws Kohana_Exception
	 * @return ORM
	 */
	public function update(Validation $validation = NULL)
	{
		parent::update($validation);

		if ($this->_search_enabled)
		{
			// Update the document to the search server.
			ElasticSearch::instance()->add_document($this->_search_type(), $this->get_search_document());
		}

		return $this;
	}

	/**
	 * Deletes a single record while ignoring relationships.
	 *
	 * @chainable
	 * @throws Kohana_Exception
	 * @return ORM
	 */
	public function delete()
	{
		if ($this->_search_enabled)
		{
			// Delete the document from the search server.
			ElasticSearch::instance()->delete_document($this->_search_type(), $this->id);
		}

		return parent::delete();
	}

} 