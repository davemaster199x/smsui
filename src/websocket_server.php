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
    use React\Socket\Server as Reactor;
    use React\Socket\SecureServer as SecureReactor;
    use React\EventLoop\Factory;

    require 'SmsuiWebSocketHandler.php';

// Determine if WSS (WebSocket Secure) should be used
    $is_secure = $config_server['tls']['enabled'] || strtolower( $config_server['ws']['method'] ) === 'wss';

    $loop = Factory::create();

    if ( $is_secure ) {
        $socket = new SecureReactor(
            new Reactor( $config_server['ws']['ip'] . ':' . $config_server['ws']['port'], $loop ),
            $loop,
            [
                'local_cert'  => $config_server['tls']['cert'],  // Path to SSL certificate
                'local_pk'    => $config_server['tls']['key'],   // Path to SSL private key
                'verify_peer' => false                           // Set to true for production
            ]
        );
        echo "WSS Server running on {$config_server['ws']['ip']}:{$config_server['ws']['port']}\n";
    } else {
        $socket = new Reactor( $config_server['ws']['ip'] . ':' . $config_server['ws']['port'], $loop );
        echo "WS Server running on {$config_server['ws']['ip']}:{$config_server['ws']['port']}\n";
    }

    $server = new IoServer(
        new HttpServer(
            new WsServer(
                new SmsuiWebSocketHandler()
            )
        ),
        $socket,
        $loop
    );

    $server->run();

?>
