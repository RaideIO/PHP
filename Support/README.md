## RaideIO's PHP API

---

This works hand-in-hand with the [RaideIOSupport](https://github.com/RaideIO/jQuery/tree/master/Support) JavaScript (and jQuery) Object.

---

### Function List

```php
__construct( $account_id, $api_key, $api_password )
comment( $id, $comment, $is_external_id )
delete( $id, $is_external_id )
get( $id, $datatype, $is_external_id )
search( $parameters )
submit( $base64_summary, $subject, $description, $requester, $external_id )
update( $id, $status, $is_external_id )
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
$RaideIO = new RaideIOSupport( RAIDE_ACCOUNT_ID, RAIDE_API_KEY, RAIDE_API_PASSWORD );
```

---

### comment() - Comment on a Support Ticket.

Parameter(s)

```
mixed   $id                 Either the Ticket ID, or an External ID.
string  $comment            The comment to post.
bool    $is_external_id     If this is an External ID, set as true.
```

Example(s)

```php
try
{
    // Post a comment based on a Ticket ID.
    $RaideIO->comment( 15, "Here's my comment." );

    // Post a comment based on an External ID.
    $RaideIO->comment( "external_15", "Here's my comment.", true );
}
catch( Exception $e )
{
    die( $e->getMessage( ) );
}
```

---

### delete() - Delete a Support Ticket.

Parameter(s)

```
mixed   $id                 Either the Ticket ID, or an External ID.
bool    $is_external_id     If this is an External ID, set as true.
```

Example(s)

```php
try
{
    // Delete a Support Ticket based on its ID.
    $RaideIO->delete( 15 );
  
    // Delete a Support Ticket based on its External ID.
    $RaideIO->delete( "external_15", true );
}
catch( Exception $e )
{
    die( $e->getMessage( ) );
}
```

---

### get() - Retrieve a specific Support Ticket.

Parameter(s)

```
mixed   $id                 Either the Ticket ID, or an External ID.
string  $datatype           Either "json" or "text" formats.
bool    $is_external_id     If this is an External ID, set as true.
```

Example(s)

```php
try
{
    // Retrieve a Support Ticket in JSON format based on its ID.
    $Ticket = $RaideIO->get( 15, "json" );
  
    // Print the results.
    print_r( $Ticket );
  
    // Retrieve a Support Ticket in text format based on its External ID.
    $Ticket = new RaideIO->get( "external_15", "text", true );
  
    // Print the results.
    print $Ticket;
}
catch( Exception $e )
{
    die( $e->getMessage( ) );
}
```

---

### search() - Search through existing Support Tickets.

Parameter(s)

```
array   $parameters     {endTime, limit, page, search, sort_by, sort_order, startTime, status}
```

Example(s)

```php
try
{
    // Retrieve all Support Tickets.
    $Tickets = $RaideIO->search( );
  
    // Print the results.
    print_r( $Tickets );
  
    // Retrieve all Support Tickets with the status "Pending".
    $Tickets = $RaideIO->search( array(
        "status" => 1
    ) );
  
    // Print the results.
    print_r( $Tickets );
}
catch( Exception $e )
{
    die( $e->getMessage( ) );
}
```

---

### submit() - Submit a new Support Ticket.

Parameter(s)

```
string  $base64_summary     A base64-encoded string provided by the RaideJS Object.
string  $subject            A subject about the error that was encountered.
string  $description        A description of the error that was encountered.
mixed   $requester          Either an e-mail address or an array {email, id, name}.
bool    $external_id        An optional ID to associate this Ticket with an ID in another system.
```

Example(s)

```php
try
{
    // Submit a new Support Ticket from the e-mail address kevin@raide.io.
    $Ticket = $RaideIO->submit( $_POST["summary"], "Does not save", "It tells me that it can not save.", "kevin@raide.io" );

    // Print the results.
    print_r( $Ticket );
  

    // Submit a new Support Ticket and associate it with a User from your application.
    $Ticket = $RaideIO->submit( $_POST["summary"], "Does not save", "It tells me that it can not save.", array(
        "email" => "kevin@raide.io",
        "id"    => 4,
        "name"  => "Kevin D"
    ) );
  
    // Print the results.
    print_r( $Ticket );
  
    // Submit a new Support Ticket, and associate it with an External ID.
    $Ticket = $RaideIO->submit( $_POST["summary"], "Does not save", "It tells me that it can not save.", "kevin@raide.io", "external_15" );
  
    // Print the results.
    print_r( $Ticket );
}
catch( Exception $e )
{
    die( $e->getMessage( ) );
}
```

---

### update() - Update the status of a Support Ticket.

Parameter(s)

```
mixed   $id                 Either the Ticket ID, or an External ID.
int     $status             The new status of this Ticket [1=Pending, 2=Open, 3=Solved]
bool    $is_external_id     If this is an External ID, set as true.
```

Example(s)

```php
try
{
    // Update the status of a Ticket to "Open", based on its ID.
    $RaideIO->update( 15, 2 );
    
    // Update the status of a Ticket to "Closed", based on its External ID.
    $RaideIO->update( "external_15", 3, true );
}
catch( Exception $e )
{
    die( $e->getMessage( ) );
}
```
