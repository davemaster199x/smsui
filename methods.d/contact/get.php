<?php namespace contact;

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


	// Get extension(s)
		if ( empty( $params['contact_id'] )) {
		// All extensions

			$contact_query = <<<SQL
  SELECT contact.*
    FROM contact
    ORDER BY contact_id DESC
SQL;

			$contact_stmt = dbh()->prepare( $contact_query );

			$contact_stmt->execute();

			$contact = $contact_stmt->fetchAll( \PDO::FETCH_ASSOC );
		} else {
		// Get specified extension details

			$contact_query = <<<SQL
  SELECT contact.*
    FROM contact
   WHERE contact_id = :contact_id
SQL;
			$contact_stmt = dbh()->prepare( $contact_query );

			$contact_stmt->bindParam( ':contact_id', $params['contact_id'], \PDO::PARAM_INT );

			$contact_stmt->execute();

			$contact = $contact_stmt->fetchAll( \PDO::FETCH_ASSOC );
		}

		return build_result( TRUE, 'contact', [ 'contact' => $contact ] );
	}

?>
