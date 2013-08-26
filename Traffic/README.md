## RaideIO's PHP Traffic API

---

This works hand-in-hand with the [RaideJS](https://github.com/RaideIO/jQuery) JavaScript (and jQuery) Object.

---

### Function List

```php
__construct( $account_id, $api_key, $api_password )
submit( $requester, $base64_queue )
```

---

### __construct() - Create a new instance of our Class.

Parameter(s)

```
int     $account_id     Your Raide Account ID.
string  $api_key        Your Raide API Key.
string  $api_password   Your Raide API Password.
```

Example(s)

```php
$RaideIO = new RaideIOTraffic( RAIDE_ACCOUNT_ID, RAIDE_API_KEY, RAIDE_API_PASSWORD );
```

---

### submit() - Submit a new Support Ticket.

Parameter(s)

```
mixed   $requester      Either an e-mail address or an array {email, id, name}.
string  $base64_queue   A base64-encoded string provided by the RaideIOTraffic Object.
```

Example(s)

```php
try
{
    // Submit all queued requests from the e-mail address kevin@raide.io.
    $submit = $RaideIO->submit( "kevin@raide.io", $_POST["queue"] );

    // Print the results.
    print_r( $submit );
  
    // Submit all queued requests and associate it with a User from your application.
    $submit = $RaideIO->submit( array(
        "email" => "kevin@raide.io",
        "id"    => 4,
        "name"  => "Kevin D"
    ), $_POST["queue"] );
  
    // Print the results.
    print_r( $submit );
}
catch( Exception $e )
{
    die( $e->getMessage( ) );
}
```
