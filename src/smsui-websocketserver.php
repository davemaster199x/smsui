<?php

// Require config and autoload
	require __DIR__ . '/../config.php';
    require __DIR__ . '/../vendor/autoload.php'; // Composer autoload

// Read in functions
    foreach ( glob( __DIR__ . '/../includes/functions.d/*.php' ) as $function ) {
		if ( substr( $function, 0, 1 ) != '.' ) {
			include( $function );
		}
	}

    use Ratchet\Server\IoServer;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;
    require 'SmsuiWebSocketHandler.php';

    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new SmsuiWebSocketHandler()
            )
        ),
        8080 // Change this port number as needed
    );

    $server->run();
