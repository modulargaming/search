<?php defined('SYSPATH') OR die('No direct access allowed.');

return array(
	'default' => array(
		'connection' => array(
			/**
			 * Connection details to search server, according to Elastica
			 * http://elastica.io/getting-started/installation.html#section-connect
			 *
			 * The following options are available:
			 *
			 * string   host       server hostname
			 * int      post       server port
			 * array    servers    array of hosts and ports
			 *
			 * Use either host and port or servers, not both at the same time.
			 */
			'host' => '127.0.0.1',
			'port' => 8200
		),
		// Default index to use with this connection.
		'index' => 'mg'
	)
);