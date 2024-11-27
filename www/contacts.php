  <?php 
    include( "{$_SERVER['DOCUMENT_ROOT']}/includes/header.php" ); 

    $jsonrpc_client = new jsonrpc\client();
    $jsonrpc_client->server( $config_client['jsonrpc']['url'] );

    $get_contacts = new jsonrpc\method( 'contact.get' );
    $get_contacts->param( 'api_token', $config_client['jsonrpc']['api_token'] );
    $get_contacts->param( 'hash',      $_SESSION['user']['hash'] );
    $get_contacts->id = $jsonrpc_client->generate_unique_id();

    $jsonrpc_client->method( $get_contacts );
    $jsonrpc_client->send();

    $result = jsonrpc\client::parse_result( $jsonrpc_client->result );

    $contacts = $result[ $get_contacts->id ]['data']['contact'];
  
  ?>

  <style>

    body, html {

        margin: 0;
        padding: 0;
        height: 100%;
        overflow: hidden;
    }
    
  </style>

        <!-- Page Sidebar Ends-->
        <div class="page-body">
          <div class="container-fluid">
            <div class="page-title">
              <div class="row">
                <div class="col-6">
                  <h3>Contacts</h3>
                </div>
              </div>
            </div>
          </div>
          <!-- Container-fluid starts-->
          <div class="container-fluid">
            <div class="row">
              <div class="col call-chat-sidebar col-sm-12">
                <div class="card">
                  <div class="card-body chat-body">
                    <div class="chat-box">
                      <!-- Chat left side Start-->
                      <div class="chat-left-aside">
                        <div class="text-center">
                          <button class="btn btn-primary" data-bs-toggle="modal" data-original-title="test" data-bs-target="#exampleModal">Add</button>
                          <a href="./new_chat.php" class="btn btn-primary">Start Chat</a>
                          <!-- <a href="./sms_new.php" class="btn btn-primary">Start Chat</a> -->
                        </div>
                        <div class="media">
                          <div class="about">
                            <div class="name f-w-600">List of Contacts</div>
                          </div>
                        </div>
                        <div class="people-list" id="people-list">
                          <div class="search">
                            <form class="theme-form">
                              <div class="mb-3">
                                <input class="form-control" type="text" id="search_contact" placeholder="Search" onkeyup="searchContacts()"><i class="fa fa-search"></i>
                              </div>
                            </form>
                          </div>
                          <ul class="list-contact"  style="overflow-y: auto; max-height: 50vh;">
                            <?php foreach ( $contacts as $contact ) : ?>
                              <li class="clearfix">
                                <div class="about">
                                  <div class="name"><a href="./sms.php?contact_id=<?=$contact['contact_id']?>"><?= ( ($contact['first_name'] != '' || $contact['last_name'] != '') ) ? $contact['first_name'].' '.$contact['last_name'] : $contact['phone'];?></a></div>
                                </div>
                              </li>
                            <?php endforeach; ?>
                          </ul>
                        </div>
                      </div>
                      <!-- Chat left side Ends-->
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
                <h5 class="modal-title" id="exampleModalLabel">Add Contact</h5>
                <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <form class="form theme-form" action="/add_contact.php" method="post">
                <div class="modal-body">
                  <div class="card-body">
                    <div class="row">
                      <div class="col">
                        <div class="mb-3">
                          <label class="form-label" for="exampleFormControlInput10">First Name</label>
                          <input class="form-control btn-square" id="first_name" name="first_name" type="text" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="mb-3">
                          <label class="form-label" for="exampleFormControlInput10">Last name</label>
                          <input class="form-control btn-square" id="last_name" name="last_name" type="text" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="mb-3">
                          <label class="form-label" for="exampleFormControlInput10">Email address</label>
                          <input class="form-control btn-square" id="email" name="email" type="email" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="mb-3">
                          <label class="form-label" for="exampleFormControlInput10">Company Name</label>
                          <input class="form-control btn-square" id="company_name" name="company_name" type="text" required>
                        </div>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col">
                        <div class="mb-3">
                          <label class="form-label" for="exampleFormControlInput10">Phone Number</label>
                          <input class="form-control btn-square" id="phone_number" name="phone_number" type="text" required>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-primary" type="button" data-bs-dismiss="modal">Close</button>
                  <button class="btn btn-secondary" type="submit">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>

  <script>
    function searchContacts() {

      var input, filter, ul, li, a, i, txtValue;
      input  = document.getElementById( "search_contact" );
      filter = input.value.toUpperCase();
      ul     = document.querySelector( ".list" );
      li     = ul.getElementsByTagName( "li" );

      for (i = 0; i < li.length; i++) {

        a = li[i].getElementsByTagName( "a" )[0];
        txtValue = a.textContent || a.innerText;
        if ( txtValue.toUpperCase().indexOf(filter) > -1 ) {

          li[i].style.display = "";
        } else {

          li[i].style.display = "none";
        }
      }
    }
</script>

  <?php include( "{$_SERVER['DOCUMENT_ROOT']}/includes/footer.php" ); ?>