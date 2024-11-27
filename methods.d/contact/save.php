<?php namespace contact;

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


		if ( empty( $params['contact_id'] )) {
		// Save new contact

			$contact_query = <<<SQL
  INSERT INTO contact
     SET user_id    = :user_id,
         first_name = :first_name,
         last_name  = :last_name,
         email      = :email,
         company    = :company,
         phone      = :phone
SQL;
			$contact_stmt = dbh()->prepare( $contact_query );

			$contact_stmt->bindParam( ':user_id',    $params['user_id'],      \PDO::PARAM_INT );
			$contact_stmt->bindParam( ':first_name', $params['first_name'],   \PDO::PARAM_STR );
			$contact_stmt->bindParam( ':last_name',  $params['last_name'],    \PDO::PARAM_STR );
			$contact_stmt->bindParam( ':email',      $params['email'],        \PDO::PARAM_STR );
			$contact_stmt->bindParam( ':company',    $params['company_name'], \PDO::PARAM_STR );
			$contact_stmt->bindParam( ':phone',      $params['phone_number'], \PDO::PARAM_STR );

			if ( $contact_stmt->execute()) {

				return build_result( TRUE, 'contact_saved', [ 'client_id' => dbh()->lastInsertId() ] );
			} else {

				return build_result( FALSE, 'contact_not_saved', [ 'error' => $contact_stmt->errorInfo() ] );
			}
		} else {
		// Update existing contact

			$contact_query = <<<SQL
  UPDATE contact
     SET user_id    = :user_id,
         first_name = :first_name,
         last_name  = :last_name,
         email      = :email,
         company    = :company,
         phone      = :phone
   WHERE contact.contact_id = :contact_id
SQL;
			$contact_stmt = dbh()->prepare( $contact_query );

			$contact_stmt->bindParam( ':user_id',    $params['user_id'],      \PDO::PARAM_INT );
			$contact_stmt->bindParam( ':first_name', $params['first_name'],   \PDO::PARAM_STR );
			$contact_stmt->bindParam( ':last_name',  $params['last_name'],    \PDO::PARAM_STR );
			$contact_stmt->bindParam( ':email',      $params['email'],        \PDO::PARAM_STR );
			$contact_stmt->bindParam( ':company',    $params['company_name'], \PDO::PARAM_STR );
			$contact_stmt->bindParam( ':phone',      $params['phone_number'], \PDO::PARAM_STR );
			$contact_stmt->bindParam( ':contact_id', $params['contact_id'],   \PDO::PARAM_INT );

			if ( $contact_stmt->execute() ) {

				return build_result( TRUE, 'contact_saved', [ 'contact_id' => $params['contact_id'] ] );
			} else {

				return build_result( FALSE, 'contact_not_saved', [ 'error' => $contact_stmt->errorInfo() ] );
			}
		}
	}

?>
