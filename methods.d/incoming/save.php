<?php namespace incoming;

	function save( $params ) {
	/*
	 * Save
	 * Save a incoming.
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
		// $user_id = \verify_hash( $params['hash'] );

		// if ( !$user_id ) {

		// 	return build_result( FALSE, 'invalid_hash' );
		// }

    // Save incoming data
        $incoming_query = <<<SQL
INSERT INTO message
    SET src      = :src,
        dst      = :dst,
        message  = :message
SQL;
        $incoming_stmt = dbh()->prepare( $incoming_query );

        $incoming_stmt->bindParam( ':src',     $params['src'],      \PDO::PARAM_STR );
        $incoming_stmt->bindParam( ':dst',     $params['dst'],      \PDO::PARAM_STR );
        $incoming_stmt->bindParam( ':message', $params['message'],  \PDO::PARAM_STR );

        if ( $incoming_stmt->execute()) {

            return build_result( TRUE, 'incoming_saved', [ 'incoming_id' => dbh()->lastInsertId() ] );
        } else {

            return build_result( FALSE, 'incoming_not_saved', [ 'error' => $incoming_stmt->errorInfo() ] );
        }
	}

?>
