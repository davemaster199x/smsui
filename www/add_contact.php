<?php include( "{$_SERVER['DOCUMENT_ROOT']}/includes/start.php" ); ?>
<?php

	$jsonrpc_client = new jsonrpc\client();
	$jsonrpc_client->server( $config_client['jsonrpc']['url'] );

	$contact = new jsonrpc\method( 'contact.save' );
	$contact->param( 'api_token',    $config_client['jsonrpc']['api_token'] );
    $contact->param( 'hash',         $_SESSION['user']['hash'] );
    $contact->param( 'user_id',      $_SESSION['user']['user_id'] );
	$contact->param( 'first_name',   $_POST['first_name'] );
	$contact->param( 'last_name',    $_POST['last_name'] );
	$contact->param( 'email',        $_POST['email'] );
	$contact->param( 'company_name', $_POST['company_name'] );
	$contact->param( 'phone_number', $_POST['phone_number'] );

	if ( !empty( $_POST['contact_id'] )) {

		$contact->param( 'contact_id', $_POST['contact_id'] );
	}

	$contact->id = $jsonrpc_client->generate_unique_id();

	$jsonrpc_client->method( $contact );
	$jsonrpc_client->send();

	$result = jsonrpc\client::parse_result( $jsonrpc_client->result );

	if ( $result[ $contact->id ]['status'] ) {
	// Add contact successfully
		if ( !empty( $_POST['contact_id'] )) {

			header( 'Location: /sms.php?contact_id='.$_POST['contact_id'] );
		} else {

			header( 'Location: /contacts.php' );
		}
	} else {

        header( 'Location: /contacts.php?error=add_contact' );
	}

?>
