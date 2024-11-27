<?php namespace message;

	function get( $params ) {
	/*
	 * Get
	 * Retrieve a list of contact.
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

	// Get messages
        $message_query = <<<SQL
  SELECT message.*
    FROM message
           INNER JOIN contact
                    ON ( REPLACE( message.src, '-', '' ) = REPLACE( contact.phone, '-', '' ) 
					   OR REPLACE( message.dst, '-', '' ) = REPLACE( contact.phone, '-', '' ) )
    WHERE contact_id = :contact_id
SQL;

        $message_stmt = dbh()->prepare( $message_query );

        $message_stmt->bindParam( ':contact_id', $params['contact_id'], \PDO::PARAM_INT );

        $message_stmt->execute();

        $message = $message_stmt->fetchAll( \PDO::FETCH_ASSOC );

		return build_result( TRUE, 'message', [ 'message' => $message ] );
	}

?>
