<?php

	function pbx_api( $method, $endpoint, $username, $password, $payload ) {
	/**
	 * A shortcut for calling the PBX API.
	 *
	 * @param endpoint array  - The endpoint details.
	 * @param method   string - The method to call.
	 * @param payload  array  - The payload to send.
	 *
	 * @return array - The result of the API call.
	 */

	// Import config
		require( \env::$paths['methods'] . '/../config.php' );

	// Import autoloader
		include_once( __DIR__ . '/pbxapi_autoloader.php' );

		pbxapi_autoloader();

	// Create the new API object
		$api = new \PBX\API();

	// Set the endpoint
		$api->endpoint(
			$config_server['api']['config']['protocol'],
			$config_server['api']['config']['server'],
			$config_server['api']['config']['port'],
			$config_server['api']['config']['version']
		);

	// Set the credentials
		$api->credentials( $username, $password );

	// Create the new Method object
		$api_method = $api->method( $method );

	// Set the parameters
		if ( in_array( $method, [ 'DeviceCreate', 'DeviceDelete', 'DeviceUpdate' ] )) {

			$api_method->set( 'mac',           $payload['mac'] );
			$api_method->set( 'name',          $payload['name'] );
			$api_method->set( 'device_type',   $payload['device_type'] );
			$api_method->set( 'http_user',     $payload['http_username'] );
			$api_method->set( 'http_password', $payload['http_password'] );
		} elseif ( $method == 'SIPEndpoints' ) {

			foreach ( $payload as $endpoint ) {

				$sipendpoints->add_endpoint( $endpoint['name'], $endpoint['password'], $endpoint['context'], $endpoint['transport'], $endpoint['callerid'], $endpoint['mailboxes'] );
			}
		}

	// Submit the request
		return $api->send();
	}

?>
