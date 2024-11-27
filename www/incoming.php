<?php
    include( "{$_SERVER['DOCUMENT_ROOT']}/includes/start.php" );

    require __DIR__ . '/../vendor/autoload.php'; // Composer autoload

    use WebSocket\Client;

    $webSocketServerAddress = $config_server['ws']['method'] . '://' . $config_server['ws']['server'] . ':' . $config_server['ws']['port'];
    
    $client = new Client( $webSocketServerAddress );
    
// Retrieve the data sent in the request
    $token   = isset( $_REQUEST['token'] )   ? $_REQUEST['token']   : '';
    $src     = isset( $_REQUEST['src'] )     ? $_REQUEST['src']     : '';
    $dst     = isset( $_REQUEST['dst'] )     ? $_REQUEST['dst']     : '';
    $message = isset( $_REQUEST['message'] ) ? $_REQUEST['message'] : '';

// Perform any desired processing with the received data
    $jsonrpc_client = new jsonrpc\client();
	$jsonrpc_client->server( $config_client['jsonrpc']['url'] );

	$save_incoming = new jsonrpc\method( 'incoming.save' );
	$save_incoming->param( 'api_token', $config_client['jsonrpc']['api_token'] );
	$save_incoming->param( 'token',     $token );
	$save_incoming->param( 'src',       $src );
	$save_incoming->param( 'dst',       $dst );
	$save_incoming->param( 'message',   $message );

	$save_incoming->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $save_incoming );
	$jsonrpc_client->send();

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

    if ( $result[ $save_incoming->id ]['status'] ) {
    // Prepare the data to be sent or processed
        $request_data = [
            'cmd'     => 'incoming',
            'token'   => $token,
            'src'     => $src,
            'dst'     => $dst,
            'message' => $message
        ];

        $json_payload = json_encode( $request_data );
        $client->send( $json_payload );
    } else {

    }
?>