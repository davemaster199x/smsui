  <?php 
    include( "{$_SERVER['DOCUMENT_ROOT']}/includes/header.php" ); 

    $jsonrpc_client = new jsonrpc\client();
    $jsonrpc_client->server( $config_client['jsonrpc']['url'] );

    $get_contacts = new jsonrpc\method( 'contact.get' );
    $get_contacts->param( 'api_token',  $config_client['jsonrpc']['api_token'] );
    $get_contacts->param( 'hash',       $_SESSION['user']['hash'] );
    $get_contacts->param( 'contact_id', $_GET['contact_id'] );
    $get_contacts->id = $jsonrpc_client->generate_unique_id();

    $jsonrpc_client->method( $get_contacts );
    $jsonrpc_client->send();

    $result = jsonrpc\client::parse_result( $jsonrpc_client->result );

    $contacts = $result[ $get_contacts->id ]['data']['contact'][0] ?? [];

    $get_messages = new jsonrpc\method( 'message.get' );
    $get_messages->param( 'api_token',  $config_client['jsonrpc']['api_token'] );
    $get_messages->param( 'hash',       $_SESSION['user']['hash'] );
    $get_messages->param( 'contact_id', $_GET['contact_id'] );
    $get_messages->id = $jsonrpc_client->generate_unique_id();

    $jsonrpc_client->method( $get_messages );
    $jsonrpc_client->send();

    $result_messages = jsonrpc\client::parse_result( $jsonrpc_client->result );

    $messages       = $result_messages[ $get_messages->id ]['data']['message'] ?? [];
    $total_messages = count( $messages );

    if ( $total_messages == 0 ) {

      $src = 0;
      $dst = 0;
    } else {

      $src = $messages[0]['src'];
      $dst = $messages[0]['dst'];
    }

    $get_dids = new jsonrpc\method( 'did.get' );
    $get_dids->param( 'api_token',  $config_client['jsonrpc']['api_token'] );
    $get_dids->param( 'hash',       $_SESSION['user']['hash'] );
    $get_dids->param( 'user_id',    $_SESSION['user']['user_id'] );
    $get_dids->id = $jsonrpc_client->generate_unique_id();

    $jsonrpc_client->method( $get_dids );
    $jsonrpc_client->send();

    $result_dids = jsonrpc\client::parse_result( $jsonrpc_client->result );

    $dids = $result_dids[ $get_dids->id ]['data']['did'] ?? [];
  ?>

  <style>
  
    body, html {

        margin: 0;
        padding: 0;
        height: 100%;
        overflow: hidden;
    }

    .page-wrapper {

        height: 100%;
    }
  </style>
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
                              <div class="name"><a href="#" data-bs-toggle="modal" data-original-title="test" data-bs-target="#exampleModal"><?= ( $contacts['first_name'] != '' || $contacts['last_name'] != '' ) ? $contacts['first_name'] : $contacts['phone'] ?></a></div>
                            </div>
                            <div class="float-end">
                              <label for="">Send From:</label>
                              <select name="did_id" id="did_id" class="" style="padding: 0.375rem 0.75rem;">
                              <?php foreach ( $dids as $did ) : ?>
                                <?php if ( $totalMessages == 0 ) { ?>
                                <option value="<?= $did['did_id']; ?>"><?= $did['did']; ?></option>
                                <?php } else {
                                   if( $messages[0]['src'] == $did['did'] ) { ?>
                                <option value="<?= $did['did_id']; ?>"><?= $did['did']; ?></option>
                                <?php } } ?>
                              <?php endforeach; ?>
                              </select>
                            </div>
                          </div>
                          <!-- chat-header end-->
                          <div id="chat-container" class="chat-history chat-msg-box custom-scrollbar" style="height: calc(70vh - 160px)">
                            <ul>
                              <?php foreach ( $messages as $message ) : ?>
                              <li <?= ( str_replace( '-', '', $contacts['phone'] ) == $message['dst'] ) ? 'class="clearfix"':''; ?>>
                                <div class="message my-message <?= ( str_replace( '-', '', $contacts['phone'] ) == $message['dst'] ) ? 'pull-right':''; ?>">
                                  <div class="message-data text-end"><span class="message-data-time"><?= ( str_replace( '-', '', $contacts['phone'] ) == $message['dst'] ) ? ( $contacts['first_name'] != '' ) ? $contacts['first_name'] : str_replace( '-', '', $contacts['phone'] ) :'Incoming'; ?></span></div><?= $message['message']; ?>
                                </div>
                              </li>
                              <?php endforeach; ?>
                              <div id="messages"></div>
                              
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

        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Update Contact</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form class="form theme-form" action="/add_contact.php" method="post">
                <div class="modal-body">
                  <div class="card-body">
                    <div class="row">
                      <div class="col">
                        <div class="mb-3">
                          <label class="form-label" for="exampleFormControlInput10">First Name</label>
                          <input class="form-control btn-square" id="first_name" name="first_name" type="text" value="<?= $contacts['first_name'] ?>" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="mb-3">
                          <label class="form-label" for="exampleFormControlInput10">Last name</label>
                          <input class="form-control btn-square" id="last_name" name="last_name" type="text" value="<?= $contacts['last_name'] ?>" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="mb-3">
                          <label class="form-label" for="exampleFormControlInput10">Email address</label>
                          <input class="form-control btn-square" id="email" name="email" type="email" value="<?= $contacts['email'] ?>" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="mb-3">
                          <label class="form-label" for="exampleFormControlInput10">Company Name</label>
                          <input class="form-control btn-square" id="company_name" name="company_name" type="text" value="<?= $contacts['company'] ?>" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="mb-3">
                          <label class="form-label" for="exampleFormControlInput10">Phone Number</label>
                          <input class="form-control btn-square" id="phone_number" name="phone_number" type="text" value="<?= $contacts['phone'] ?>" required>
                          <input class="form-control btn-square" id="contact_id" name="contact_id" type="hidden" value="<?= $contacts['contact_id'] ?>">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Close</button>
                  <button class="btn btn-secondary" type="submit">Update</button>
                </div>
              </form>
            </div>
          </div>
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

            const receivedData   = JSON.parse( event.data );
            const status         = receivedData.status;
            const message        = receivedData.content;
            const cmd            = receivedData.cmd;
            const src            = receivedData.src;
            const dst            = receivedData.dst;
            const api_response   = receivedData.api_response;
            console.log( 'Received message:', message );

            if ( cmd == 'outgoing' ) {

                if( status == 1 ){

              // Display the received message on the web page
                const messagesDiv = document.getElementById( 'messages' );
                messagesDiv.innerHTML += `
                <li class="clearfix">
                    <div class="message my-message pull-right">
                        <div class="message-data text-end">
                            <span class="message-data-time"><?= $contacts['first_name']; ?></span>
                        </div>
                        <p>${ message }</p>
                    </div>
                </li>`;
              } else {

                console.log( api_response );
              }
            } else {

              var send_from  = <?= $src ?>;
              var src_from   = <?= $dst ?>;

              if ( send_from ==  dst && src_from == src ) {
                // Display the received incoming message from web
                  const messagesDiv = document.getElementById( 'messages' );
                  messagesDiv.innerHTML += `
                  <li class="clearfix">
                      <div class="message my-message">
                          <div class="message-data text-end">
                              <span class="message-data-time">Incoming</span>
                          </div>
                          <p>${ message }</p>
                      </div>
                  </li>`;
              }
            }
            

          // Clear message input
            document.getElementById( 'message_to_send' ).value = '';

            scrollToBottom();
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

          const messageToSend = document.getElementById( 'message_to_send' ).value;
          const did_id        = document.getElementById( 'did_id' ).value;
          
          const payload = {
              cmd:       'outbound_message',
              did_id:     did_id,
              recipient:  <?= preg_replace( '/[^0-9]/', '', $contacts['phone'] ) ?>,
              content:    messageToSend
          };
          socket.send( JSON.stringify(payload) );
        }

      // This function scrolls the chat history container to the bottom
        function scrollToBottom() {

          var chatContainer = document.getElementById("chat-container");
          chatContainer.scrollTop = chatContainer.scrollHeight;
        }
        
      // Scroll to the bottom when the page finishes loading
        window.onload = scrollToBottom;

    </script>
    <?php include( "{$_SERVER['DOCUMENT_ROOT']}/includes/footer.php" ); ?>