 <?php 
    include( "{$_SERVER['DOCUMENT_ROOT']}/includes/header.php" ); 

    $jsonrpc_client = new jsonrpc\client();
    $jsonrpc_client->server( $config_client['jsonrpc']['url'] );

    $get_dids = new jsonrpc\method( 'did.get' );
    $get_dids->param( 'api_token',  $config_client['jsonrpc']['api_token'] );
    $get_dids->param( 'hash',       $_SESSION['user']['hash'] );
    $get_dids->param( 'user_id',    $_SESSION['user']['user_id'] );
    $get_dids->id = $jsonrpc_client->generate_unique_id();

    $jsonrpc_client->method( $get_dids );
    $jsonrpc_client->send();

    $result_dids = jsonrpc\client::parse_result( $jsonrpc_client->result );

    $dids = $result_dids[ $get_dids->id ]['data']['did'];
  
  ?>

        <!-- Page Sidebar Ends-->
        <div class="page-body">
          <div class="container-fluid">
            <div class="page-title">
              <div class="row">
                <div class="col-6">
                    <a href="contacts.php">< Go back</a>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
              <div class="col call-chat-body">
                <div class="card">
                  <div class="card-body p-0">
                    <div class="row chat-box">
                      <!-- Chat right side start-->
                      <div class="col pe-0 chat-right-aside">
                        <!-- chat start-->
                        <div class="chat">
                          <!-- chat-header start-->
                          <div class="chat-header clearfix">
                            <div class="about">
                              <div class="name">
                                <button id="addButton" class="btn btn-success">+</button>
                              </div>
                              <div id="number_container">
                                <input type="text" class="form-control" value="" id="new_number[]" name="new_number[]" placeholder="Enter Number" style="width: 130px;">
                              </div>
                            </div>
                            <div class="float-end">
                              <label for="">Send From:</label>
                              <select name="did_id" id="did_id" class="" style="padding: 0.375rem 0.75rem;">
                              <?php foreach ( $dids as $did ) : ?>
                                <option value="<?= $did['did_id']; ?>"><?= $did['did']; ?></option>
                              <?php endforeach; ?>
                              </select>
                            </div>
                          </div>
                          <!-- chat-header end-->
                          <div class="chat-history chat-msg-box custom-scrollbar">
                            <ul>
                              <div id="loadingDiv" class="loading" style="display: none;">
                                    Sending...
                                </div>
                            </ul>
                          </div>
                          <!-- end chat-history-->
                          <div class="chat-message clearfix">
                            <div class="row">
                              <div class="col-xl-12 d-flex">
                                <div class="smiley-box bg-primary">
                                  <div class="picker"><img src="./assets/images/smiley.png" alt=""></div>
                                </div>
                                <div class="input-group text-box">
                                  <input class="form-control input-txt-bx" id="message_to_send" type="text" name="message_to_send" placeholder="Type a message......">
                                  <button class="input-group-text btn btn-primary" onclick="sendMessage()" type="button">SEND</button>
                                </div>
                              </div>
                            </div>
                          </div>
                          <!-- end chat-message-->
                          <!-- chat end-->
                          <!-- Chat right side ends-->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid Ends-->
        </div>

    <script>
      // Establish a WebSocket connection
        const socket = new WebSocket( '<?= $config_server['ws']['method'] . '://' . $config_server['ws']['server'] . ':' . $config_server['ws']['port']; ?>' ); // Replace with your WebSocket server address

      // Event handler when the connection is established
        socket.onopen = ( event ) => {

            console.log( 'WebSocket connection established!' );
        };
      // Event handler to handle incoming messages from the server
        socket.onmessage = ( event ) => {
          
            const message = event.data;
            console.log( message );
        };

      // Event handler when an error occurs with the WebSocket connection
        socket.onerror = ( event ) => {

            console.error('WebSocket error:', event);
        };

      // Event handler when the connection is closed
        socket.onclose = ( event ) => {

            console.log( 'WebSocket connection closed:', event );
        };

      // Function to send a message to the server
        function sendMessage() {
            const messageToSend   = document.getElementById('message_to_send').value;
            const did_id          = document.getElementById('did_id').value;
            const newNumberInputs = document.getElementsByName('new_number[]'); // Select all new_number[] inputs
            const totalNewNumbers = newNumberInputs.length;

            const messagetype  = ( totalNewNumbers == 1 ) ? 'sms': 'mms';
            const payloadArray = [];

            $( '#loadingDiv' ).show();

            setTimeout(function() {
            // Loop through the new_number[] inputs and create payload objects
              newNumberInputs.forEach( input => {

                  const recipient = input.value.replace(/[^0-9]/g, '');
                  const payload   = {
                      cmd:         'new_outbound_message',
                      user_id:     <?= $_SESSION['user']['user_id'] ?>,
                      did_id:      did_id,
                      recipient:   recipient,
                      messagetype: messagetype,
                      content:     messageToSend
                  };
                  
                  payloadArray.push( payload );
              });

            // Send each payload through the socket
              let messagesSent = 0;

              payloadArray.forEach( payload => {

                  socket.send( JSON.stringify( payload ) );
                  messagesSent++;

                // Check if all messages have been sent
                  if ( messagesSent === payloadArray.length ) {
                    // All messages have been sent, hide the loadingDiv
                      $( '#loadingDiv' ).hide();
                      alert( 'Successfully sent messages!' );
                      window.location.href = '/contacts.php';
                  }
              });
            }, 2000);
        }


        document.getElementById( 'addButton' ).addEventListener( 'click' , function() {

            var input         = document.createElement( 'input' );
            input.type        = 'text';
            input.className   = 'form-control';
            input.name        = 'new_number[]';
            input.placeholder = 'Enter Number';
            input.style.width = '130px';

            document.getElementById( 'number_container' ).appendChild(input);
        });
    </script>
    <?php include( "{$_SERVER['DOCUMENT_ROOT']}/includes/footer.php" ); ?>