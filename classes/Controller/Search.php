<?php defined('SYSPATH') OR die('No direct script access.');

class Controller_Search extends Abstract_Controller_Frontend {

	public function action_index()
	{

		$this->view = new View_Search;

		if ($this->request->query('query') !== NULL)
		{

			$query = $this->request->query('query');

			$queryString  = new \Elastica\Query\QueryString();
			$queryString->setDefaultOperator('AND');
			$queryString->setQuery($query);

			$elasticaQuery  = new \Elastica\Query();
			$elasticaQuery->setQuery($queryString);

			$resultSet = ElasticSearch::instance()->search($elasticaQuery);
			$results  = $resultSet->getResults();

			/** @var Kostache $renderer */
			$renderer = Kostache::factory();
			$out = array();
			foreach ($results as $result)
			{

				// Attempt to create the avatar instance.
				try
				{
					$refl = new ReflectionClass('View_Search_'.ucfirst($result->getType()));
					$view = $refl->newInstance();

					$view->data = $result->getData();
					$out[] = $renderer->render($view);
				}
				catch (ReflectionException $ex)
				{
					Kohana::$log->add(LOG::ERROR, 'No search view class found for search type ":type".', array(':type' => $result->getType()));
				}
			}

			$this->view->query = $query;
			$this->view->results = $out;
		}
	}

} 