# [Modular Gaming Search](http://www.modulargaming.com)

Search is a module for [Modular Gaming](https://github.com/modulargaming/modulargaming), a modular [persistent browser based game](http://www.pbbg.org) framework.

It implements a search system using [ElasticSearch](http://www.elasticsearch.org/).

## Requirements

* PHP 5.4+
* ElasticSearch
* [Composer](http://getcomposer.org) (Dependency Manager)

## Installation

Search is installed using composer, simply add it as a dependency to your ```composer.json``` file:
```javascript
{
	"require": {
		"modulargaming/search": "~0.1.0"
	}
}
```

Copy  the configuration file, config/search.php to your application directory and edit it to match your settings.

## Using


### Models, Saving
Searchable models need to use the Model_ElasticSearch trait, and implement the functions ```get_search_document``` and ```send_search_mapping```.

```php
class Model_User extends MG_Model_User {
	use Model_ElasticSearch;

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
}
```

The mapping can either be set with the Minion Task ```php ./minion Search::mapping --Model=User``` or manually
entered into ElasticSearch.

All changes to a searchable model will also send the matching queries to ElasticSearch to keep them matching.

Worth noting is that ALL changes will trigger a search query update, this can be changed by overwriting the update
function to only call the trait::update if related property was changed.

The search indexes can be rebuilt using the Minion Task ```php ./minion Search::index --Model=User```.

### Search page, Retrieving

The search page currently searches the whole index for matching records. Each search result gets parsed with a view
class matching the search type (_type).

## Examples

The example User model. Notice we only trigger the update if the username property was changed.
```php
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
```
