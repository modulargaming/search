<?php defined('SYSPATH') OR die('No direct script access.');
/**
 * Elastica wrapper, supports multiple instances.
 */
class ElasticSearch {

	/**
	 * @var  string  default instance name
	 */
	public static $default = 'default';

	private static $_instances = array();

	/**
	 * @param   string   $name    instance name
	 * @param   array    $config  configuration parameters
	 * @return  ElasticSearch
	 */
	public static function instance($name = NULL, array $config = NULL)
	{
		if ($name === NULL)
		{
			$name = ElasticSearch::$default;
		}

		if ( ! isset(ElasticSearch::$_instances[$name]))
		{
			if ($config === NULL)
			{
				$config = Kohana::$config->load('search')->$name;
			}

			ElasticSearch::$_instances[$name] = new ElasticSearch($name, $config);
		}

		return ElasticSearch::$_instances[$name];
	}

	/** @var \Elastica\Client */
	private $_client;

	/** @var \Elastica\Index */
	private $_index;

	/**
	 * @param $name
	 * @param $config
	 */
	public function __construct($name, $config)
	{
		$this->_client = new \Elastica\Client(array(
			$config['connection']
		));

		$this->_index = $this->_client->getIndex($config['index']);
	}

	/**
	 * @param $index
	 * @return \Elastica\Index
	 */
	public function get_index($index)
	{
		return $this->_client->getIndex($index);
	}

	/**
	 * Get the specified type from the index.
	 *
	 * @param $type
	 * @return \Elastica\Type
	 */
	public function get_type($type)
	{
		return $this->_index->getType($type);
	}

	/**
	 * Add the specified document of the specific type to the index.
	 *
	 * @param $type
	 * @param \Elastica\Document $document
	 */
	public function add_document($type, \Elastica\Document $document)
	{
		$this->get_type($type)->addDocument($document);
	}

	/**
	 * Add the specified documents of the specific type to the index.
	 *
	 * @param $type
	 * @param \Elastica\Document[] $documents
	 */
	public function add_documents($type, array $documents)
	{
		$this->get_type($type)->addDocuments($documents);
	}

	/**
	 * Delete the document with the id.
	 *
	 * @param $type
	 * @param $id
	 */
	public function delete_document($type, $id)
	{
		$this->get_type($type)->deleteById($id);
	}

	/**
	 * Run a search query.
	 *
	 * @param \Elastica\Query $query
	 * @return \Elastica\ResultSet
	 */
	public function search(\Elastica\Query $query)
	{
		return $this->_index->search($query);
	}

} 