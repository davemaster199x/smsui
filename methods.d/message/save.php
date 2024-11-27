<?php namespace message;

	function save( $params ) {
	/*
	 * Save
	 * Save a client.
	 */

	require( \env::$paths['methods'] . '/../config.php' );

	\function_init( [ 'build_result', 'check_api_token', 'dbh', 'verify_hash', 'audit_log' ] );

	// Verify authorized API Token
		if ( empty( $params['api_token'] )) {

			return build_result( FALSE, 'api_token_missing' );
		}

		if ( !\check_api_token( $params['api_token'] )) {

			return build_result( FALSE, "api_token_failure: {$params['api_token']}" );
		}

        audit_log( 0, __NAMESPACE__ . '\\' . __FUNCTION__, json_encode( $params ) );

	// Verify hash
		$user_id = \verify_hash( $params['hash'] );

		if ( !$user_id ) {

			return build_result( FALSE, 'invalid_hash' );
		}


		if ( empty( $params['message_id'] )) {
		// Save new contact

			$message_query = <<<SQL
  INSERT INTO message
     SET src     = :src,
         dst     = :dst,
         type    = :type,
         message = :message
SQL;
			$contact_stmt = dbh()->prepare( $message_query );

			$message_stmt->bindParam( ':src',     $params['src'],     \PDO::PARAM_STR );
			$message_stmt->bindParam( ':dst',     $params['dst'],     \PDO::PARAM_STR );
			$message_stmt->bindParam( ':type',    $params['type'],    \PDO::PARAM_STR );
			$message_stmt->bindParam( ':message', $params['message'], \PDO::PARAM_STR );

			if ( $message_stmt->execute()) {

				return build_result( TRUE, 'message_saved', [ 'message_id' => dbh()->lastInsertId() ] );
			} else {

				return build_result( FALSE, 'message_not_saved', [ 'error' => $message_stmt->errorInfo() ] );
			}
		} else {
		// Update existing client

// 			$client_query = <<<SQL
//   UPDATE client
//      SET name          = :name,
//          http_username = :http_username,
//          http_password = :http_password
//    WHERE client_id = :client_id
// SQL;
// 			$client_stmt = dbh()->prepare( $client_query );

// 			$client_stmt->bindParam( ':name',          $params['name'],          \PDO::PARAM_STR );
// 			$client_stmt->bindParam( ':http_username', $params['http_username'], \PDO::PARAM_STR );
// 			$client_stmt->bindParam( ':http_password', $params['http_password'], \PDO::PARAM_STR );
// 			$client_stmt->bindParam( ':client_id',     $params['client_id'],     \PDO::PARAM_INT );

// 			if ( $client_stmt->execute() ) {

// 				return build_result( TRUE, 'client_saved', [ 'client_id' => $params['client_id'] ] );
// 			} else {

// 				return build_result( FALSE, 'client_not_saved', [ 'error' => $client_stmt->errorInfo() ] );
// 			}
		}
	}

?>
