<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class SmsuiWebSocketHandler implements MessageComponentInterface {

    protected $clients;

    public function __construct() {

        $this->clients = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn) {
    // Store the new connection when it's opened
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onClose(ConnectionInterface $conn) {
    // Remove the connection when it's closed
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
    // Handle any errors that occur on the connection
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg) {

        $payload = json_decode($msg, true); // Decode the JSON string into an associative array

        if ( $payload['cmd'] == 'outbound_message' ) {

            $did_id     = $payload['did_id'];
            $recipient  = $payload['recipient'];
            $content    = $payload['content'];
            
$did_query = <<<SQL
  SELECT *
    FROM did
   WHERE did_id = :did_id
SQL; 
			$did_stmt = dbh()->prepare( $did_query );
            
			$did_stmt->bindParam( ':did_id',    $did_id,    \PDO::PARAM_INT );
            $did_stmt->execute();
            
            $did_row = $did_stmt->fetch( \PDO::FETCH_ASSOC );

            ini_set( "log_errors", 1 );
            ini_set( "error_log", "C:/xampp/smsui/error/error_log_file.log" );

            $ch = curl_init();

            curl_setopt( $ch, CURLOPT_URL, $did_row['api_endpoint'] );
            curl_setopt( $ch, CURLOPT_HEADER, TRUE );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
            curl_setopt( $ch, CURLOPT_POST, TRUE );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, [
                'username'  => $did_row['api_username'],
                'password'  => $did_row['api_password'],
                'sender'    => $did_row['did'],
                'recipient' => $recipient,
                'message'   => $content
            ] );

        // Set SSL verification options to false (not recommended for production)
            // curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
            // curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );

            $data = curl_exec( $ch );

            if ( $data === false ) {
            // If there is an error in the cURL request, log the error
                $error_message = curl_error( $ch );
                error_log( "cURL Error: $error_message" );
            }

            $header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
            $headers     = substr( $data, 0, $header_size );
            $body        = substr( $data, $header_size );
            $response    = json_decode( $body, TRUE );

        // Log the full response for debugging purposes
            error_log( "API Response: " . print_r( $response, true ) );

        // Close cURL session
            curl_close( $ch );

            if ( $response['message']  == 'Message successfully sent.') {
            
                $type = 'sms';

$message_query = <<<SQL
   INSERT INTO message
    SET src     = :src,
        dst     = :dst,
        type    = :type,
        message = :message
SQL;    
                $message_stmt = dbh()->prepare( $message_query );
                
                $message_stmt->bindParam( ':src',     $did_row['did'], \PDO::PARAM_STR );
                $message_stmt->bindParam( ':dst',     $recipient,      \PDO::PARAM_STR );
                $message_stmt->bindParam( ':type',    $type,           \PDO::PARAM_STR );
                $message_stmt->bindParam( ':message', $content,        \PDO::PARAM_STR );
                $message_stmt->execute();

                $payloadWithContent = [
                    'status'       => 1,
                    'cmd'          => 'outgoing',
                    'src'          => '',
                    'dst'          => '',
                    'content'      => $content,
                    'api_response' => $response['message']
                ];

                $jsonPayloadWithContent = json_encode( $payloadWithContent );

                $from->send( $jsonPayloadWithContent );
            } else {

                $payloadWithContent = [
                    'status'       => 0,
                    'cmd'          => 'outgoing',
                    'src'          => '',
                    'dst'          => '',
                    'content'      => $content,
                    'api_response' => $response['message']
                ];

                $jsonPayloadWithContent = json_encode( $payloadWithContent );

                $from->send( $jsonPayloadWithContent );
            }

        } elseif ( $payload['cmd'] == 'new_outbound_message' ) {
          
            $user_id    = $payload['user_id'];
            $did_id     = $payload['did_id'];
            $recipient  = $payload['recipient'];
            $content    = $payload['content'];

$did_query = <<<SQL
  SELECT *
    FROM did
   WHERE did_id = :did_id
SQL; 
            $did_stmt = dbh()->prepare( $did_query );
            
            $did_stmt->bindParam( ':did_id',    $did_id,    \PDO::PARAM_INT );
            $did_stmt->execute();
            
            $did_row = $did_stmt->fetch( \PDO::FETCH_ASSOC );

            ini_set( "log_errors", 1 );
            ini_set( "error_log", "C:/xampp/smsui/error/error_log_file.log" );

            $ch = curl_init();

            curl_setopt( $ch, CURLOPT_URL, $did_row['api_endpoint'] );
            curl_setopt( $ch, CURLOPT_HEADER, TRUE );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
            curl_setopt( $ch, CURLOPT_POST, TRUE );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, [
                'username'  => $did_row['api_username'],
                'password'  => $did_row['api_password'],
                'sender'    => $did_row['did'],
                'recipient' => $recipient,
                'message'   => $content
            ] );

        // Set SSL verification options to false (not recommended for production)
            // curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
            // curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false );

            $data = curl_exec( $ch );

            if ( $data === false ) {
            // If there is an error in the cURL request, log the error
                $error_message = curl_error( $ch );
                error_log( "cURL Error: $error_message" );
            }

            $header_size = curl_getinfo( $ch, CURLINFO_HEADER_SIZE );
            $headers     = substr( $data, 0, $header_size );
            $body        = substr( $data, $header_size );
            $response    = json_decode( $body, TRUE );

        // Log the full response for debugging purposes
            error_log( "API Response: " . print_r( $response, true ) );

        // Close cURL session
            curl_close( $ch );

            if ( $response['message']  == 'Message successfully sent.') {

$contact_query = <<<SQL
  SELECT contact.*
    FROM contact
   WHERE phone = :phone
SQL;
			$contact_stmt = dbh()->prepare( $contact_query );

			$contact_stmt->bindParam( ':phone', $recipient, \PDO::PARAM_INT );

			$contact_stmt->execute();


                if ( $contact_stmt->rowCount() == 1 ) {
                    // do nothing
                } else {

$contact_query = <<<SQL
  INSERT INTO contact
     SET user_id   = :user_id,
         phone     = :phone
SQL;    
                $contact_stmt = dbh()->prepare( $contact_query );
                
                $contact_stmt->bindParam( ':user_id',   $user_id,   \PDO::PARAM_INT );
                $contact_stmt->bindParam( ':phone',     $recipient, \PDO::PARAM_STR );
                $contact_stmt->execute();  
                
                }

                $type = $payload['messagetype'];
                
$message_query = <<<SQL
  INSERT INTO message
    SET src     = :src,
        dst     = :dst,
        type    = :type,
        message = :message
SQL;    
                $message_stmt = dbh()->prepare( $message_query );
                
                $message_stmt->bindParam( ':src',     $did_row['did'], \PDO::PARAM_STR );
                $message_stmt->bindParam( ':dst',     $recipient,      \PDO::PARAM_STR );
                $message_stmt->bindParam( ':type',    $type,           \PDO::PARAM_STR );
                $message_stmt->bindParam( ':message', $content,        \PDO::PARAM_STR );
                $message_stmt->execute();

                $from->send( "success" );
                  
            } else {
                
                $from->send( "invalid" );
            }
        } elseif ( $payload['cmd'] == 'incoming' ) {

            foreach ( $this->clients as $client ) {
                    
                $payloadWithContent = [
                    'status'  => 1,
                    'cmd'     => 'incoming',
                    'src'     => $payload['src'],
                    'dst'     => $payload['dst'],
                    'content' => $payload['message']
                ];

                $jsonPayloadWithContent = json_encode( $payloadWithContent );

                $client->send( $jsonPayloadWithContent );
            }
        }
    } 
}
