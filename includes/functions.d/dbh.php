<?php

	function dbh() {
	/**
	 * Establishes a connection to the database and returns the PDO object, or
	 * returns the already opened object if it exists, or returns FALSE if
	 ( unable to establish a connection.
	 *
	 * @return PDO - The open database connection.
	 */

	// Initialize $pdo to null the first time this function is being called
		static $pdo = null;
		static $err = 0;

		if ( $pdo === null ) {
		// This is the first call to this function, establish a new connection

			global $config_server;

			if ( empty( $config_server['db']['port'] ) ) { 
			// No port is set in config, set a default value

				$config_server['db']['port'] = 3306;
			}

			try {
			// Establish a new connection
				$pdo = new PDO(
					"mysql:" .
					"host={$config_server['db']['host']};" .
					"port={$config_server['db']['port']};" .
					"dbname={$config_server['db']['name']}",
					$config_server['db']['user'],
					$config_server['db']['pass'],
					[ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ]
				);

			// Set to UTF8
				$pdo->query( "SET NAMES 'utf8'" );
			} catch( PDOException $e ) {
			// Couldn't establish a connection

				echo "DBH: Can't establish connection: " . print_r( $e, TRUE );

				return FALSE;
			}
		} elseif ( $pdo instanceof PDO ) {
		// Check if the connection is still valid

			try {
				@$pdo->query( 'SELECT 1' );
			} catch ( PDOException $e ) {
				if ( $e->errorInfo[2] == 'MySQL server has gone away' && $err < 2 ) {

				// Increment error counter to preventive infinite recursion
					++$err;

				// Sleep for half a second to see if the MySQL server comes back
					usleep( 500000 );

				// Re-establish the broken connection
					$pdo = null;
					$pdo = dbh();
				} else {
				// Couldn't re-establish connection
					echo "DBH: Can't re-establish connection: " . print_r( $e, TRUE );

					return FALSE;
				}
			}
		}

		return $pdo;
	}

?>
