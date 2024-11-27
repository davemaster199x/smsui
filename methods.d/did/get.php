<?php namespace did;

	function get( $params ) {
	/*
	 * Get
	 * Retrieve a list of did.
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

	// Get did
    if ( empty( $params['did_id'] )) {

        $did_query = <<<SQL
  SELECT did.*
    FROM xref_user_did
           INNER JOIN did
                    ON xref_user_did.did_id = did.did_id
    WHERE xref_user_did.user_id = :user_id
SQL;

        $did_stmt = dbh()->prepare( $did_query );

        $did_stmt->bindParam( ':user_id', $params['user_id'], \PDO::PARAM_INT );

        $did_stmt->execute();

        $did = $did_stmt->fetchAll( \PDO::FETCH_ASSOC );
    } else {

        $did_query = <<<SQL
  SELECT did.*
    FROM xref_user_did
           INNER JOIN did
                    ON xref_user_did.did_id = did.did_id
    WHERE did.did_id = :did_id
SQL;

        $did_stmt = dbh()->prepare( $did_query );

        $did_stmt->bindParam( ':did_id', $params['did_id'], \PDO::PARAM_INT );

        $did_stmt->execute();

        $did = $did_stmt->fetchAll( \PDO::FETCH_ASSOC );
    }
    
		return build_result( TRUE, 'did', [ 'did' => $did ] );
	}

?>
