<?php include( "{$_SERVER['DOCUMENT_ROOT']}/includes/start.php" ); ?>
<?php

	unset( $_SESSION['user'] );

	header( 'Location: /' );

?>
