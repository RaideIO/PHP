<?php

/**
 * RaideIO API
 *
 * @funcs       __construct( $account_id, $api_key, $api_password )
 *		__parse_results( $results )
 *		__request( $httpMethod, $url, $parameters )
 *		comment( $id, $comment, $is_external_id )
 * 		delete( $id, $is_external_id )
 *		get( $id, $datatype, $is_external_id )
 *		search( $parameters )
 *		submit( $base64_summary, $subject, $description, $requester, $external_id )
 *		update( $id, $status, $is_external_id )
 */

class RaideIO
{	
	private $accountId 	= 0;
	private $apiKey 	= '';
	private $apiPassword 	= '';
	
	private $endPoint = 'http://api.raide.io/1.0/';

	/**
	 * When an instance of this class is initialized.
	 *
	 * @param	int		$account_id		Raide Account ID.
	 * @param	string		$api_key		Raide API Key.
	 * @param	string		$api_password		Raide API Password.
	 */

	public function __construct( $account_id = 0, $api_key = '', $api_password = '' )
	{
		$this->accountId	= $account_id;
		$this->apiKey 		= $api_key;
		$this->apiPassword 	= $api_password;
	}

	/**
	 * Check whether or not the cURL request was successful.
	 *
	 * @param	array	[httpCode, rawResponse]
	 * @return	array
	 */

	private function __parse_results( $results )
	{
		// What HTTP code was returned?
		$httpCode 	= $results[0];
		$rawResponse 	= $results[1];
		
		// Check which error code could have been thrown.
		$codes = array(
			401 => 'You are Unauthorized.',
			403 => 'You are Forbidden.'
		);
		
		// If a 200 HTTP code was returned.
		if ( $httpCode == 200 )
		{
			// Determine if no errors occurred, returned in format {error, errorDescription, result}
			$returned = json_decode( $rawResponse, true );
			
			// If no errors occurred.
			if ( $returned['error'] == 0 )
			{
				return $returned['result'];
			}
			// If an error occurred.
			else
			{
				$error = $returned['errorDescription'];
			}
		}
		// If an error code was found.
		elseif ( array_key_exists( $httpCode, $codes ) )
		{
			$error = $codes[ $httpCode ];
		}
		
		// If no error has been set.
		if ( !isset( $error ) )
		{
			$error = 'An error has occurred.';
		}
		
		throw new Exception( $error );
	}
	
	/**
	 * Execute a cURL request.
	 *
	 * @param	string	$httpMethod	[DELETE|GET|POST|PUT]
	 * @param	string	$url			
	 * @param	array	$parameters
	 * @return	void
	 */
		
	private function __request( $httpMethod, $url, $parameters = array( ) ) 
	{
		$builtUrl = $this->endPoint . $url;
		
		// If this is a GET request, and an array of parameters were provided.
		if ( $httpMethod == 'GET' && is_array( $parameters ) && sizeof( $parameters ) )
		{
			$builtUrl .= '?' . http_build_query( $parameters );
		}
		
		// Initialize a cURL request.
		$curl = curl_init( $builtUrl );
		
		curl_setopt( $curl, CURLINFO_HEADER_OUT, 1 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $curl, CURLOPT_USERAGENT, 'Raide/1.0 (PHP)' );
		
		// Append an Authentication header with the Account's ID, API Key and API Password.
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 
			'Authentication: id=' . $this->accountId . ';key=' . $this->apiKey . ';password=' . $this->apiPassword
		) );
		
		// If this is either a DELETE, POST or PUT request.
		if ( $httpMethod != 'GET' ) 
		{
			// If this is a POST request.
			if ( $httpMethod == 'POST' ) 
			{
				curl_setopt( $curl, CURLOPT_POST, 1 );
			}
			// If this is a DELETE or PUT request.
			else 
			{
				curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, $httpMethod );
			}
			
			// If parameters were provided, pass them to the server.
			if ( !is_null( $parameters ) ) 
			{
				curl_setopt( $curl, CURLOPT_POSTFIELDS, http_build_query( $parameters ) );
			}
		}
		
		// Fetch the returned data from this cURL request.
		$rawResponse = curl_exec( $curl );
		
		// What HTTP Code was returned?
		$httpCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		
		// Close the cURL request.
		curl_close( $curl );
		
		// Return both the HTTP Code and the Raw Response.
		return array(
			$httpCode,
			$rawResponse
		);
	}
	
	/**
	 * Post a Comment.
	 *
	 * @param	int	$id			Either the Ticket ID, or an External ID.
	 * @param	string	$comment		The comment to post.
	 * @param	bool	$is_external_id		If this is an External ID, set as true.
	 * @return	array	
	 */
	
	public function comment( $id = 0, $comment = '', $is_external_id = false )
	{
		try
		{
			$parameters = array( 
				'comment' => $comment
			);
			
			// Parse the results of this cURL request.
			return $this->__parse_results( $this->__request( 'POST', 'comment/' . $id, $parameters ) );
		}
		// If an error occurred.
		catch( Exception $e )
		{
			throw new Exception( $e->getMessage( ) );
		}
	}
	
	/**
	 * Delete a Ticket.
	 *
	 * @param	mixed	$id			Either the Ticket ID, or an External ID.
	 * @param	bool	$is_external_id		If this is an External ID, set as true.
	 * @return	bool
	 */
	
	public function delete( $id = 0, $is_external_id = false )
	{
		try
		{
			$url_append = ( $is_external_id == true ? '/external' : '' ) . '/' . $id;
			
			// Parse the results of this cURL request.
			return $this->__parse_results( $this->__request( 'DELETE', 'delete' . $url_append, NULL ) );
		}
		// If an error occurred.
		catch( Exception $e )
		{
			throw new Exception( $e->getMessage( ) );
		}
	}
	
	/**
	 * Retrieve a Ticket.
	 *
	 * @param	mixed	$id			Either the Ticket ID, or an External ID.
	 * @param	string	$datatype		[json|text]
	 * @param	bool	$is_external_id		If this is an External ID, set as true.
	 * @return	array
	 */
	
	public function get( $id = NULL, $datatype = 'json', $is_external_id = false )
	{
		try
		{
			$url_append = ( $is_external_id == true ? '/external' : '' ) . '/' . $id . '/' . $datatype;
			
			// Parse the results of this cURL request.
			return $this->__parse_results( $this->__request( 'GET', 'get' . $url_append, NULL ) );
		}
		// If an error occurred.
		catch( Exception $e )
		{
			throw new Exception( $e->getMessage( ) );
		}
	}
	
	/**
	 * Search through existing Tickets.
	 *
	 * @param	array	$parameters
	 * @return	array
	 */
	
	public function search( $parameters )
	{
		try
		{
			$possible = array( 'endTime', 'limit', 'page', 'search', 'sort_by', 'sort_order', 'startTime', 'status' );
			$sending  = array( );
			
			// Determine which parameters we should pass to the server.
			foreach( $possible as $key )
			{
				if ( array_key_exists( $key, $parameters ) )
				{
					$sending[ $key ] = $parameters[ $key ];
				}
			}
			
			// Parse the results of this cURL request.
			return $this->__parse_results( $this->__request( 'GET', 'search', $sending ) );
		}
		// If an error occurred.
		catch( Exception $e )
		{
			throw new Exception( $e->getMessage( ) );
		}
	}
	
	/**
	 * Submit a Ticket.
	 *
	 * @param	string	$base64_summary		A base64-encoded string provided by the RaideJS Object.
	 * @param	string	$subject		A subject about the bug that was encountered.
	 * @param	string	$description		A description of the bug that was encountered.
	 * @param	mixed	$requester		Either an e-mail address, or an array {id, email, name}.
	 * @param	string	$external_id		An optional D to associate this Ticket with an ID in another system.
	 * @return	array
	 */
	
	public function submit( $base64_summary, $subject, $description, $requester, $external_id = '' )
	{
		try
		{
			$parameters = array( 
				'description'	=> $description,
				'external_id' 	=> $external_id, 
				'requester' 	=> $requester, 
				'server' 	=> $_SERVER,
				'subject'	=> $subject,
				'summary'	=> $base64_summary
			);
			
			// Parse the results of this cURL request.
			return $this->__parse_results( $this->__request( 'POST', 'submit', $parameters ) );
		}
		// If an error occurred.
		catch( Exception $e )
		{
			throw new Exception( $e->getMessage( ) );
		}
	}
	
	/**
	 * Update the status of a Ticket.
	 *
	 * @param	mixed	$id			Either the Ticket ID, or an External ID.
	 * @param	int	$status			[1=Pending, 2=Open, 3=Solved]
	 * @param	bool	$is_external_id		If this is an External ID, set as true.
	 */
	
	public function update( $id = NULL, $status = 1, $is_external_id = false )
	{
		try
		{
			$parameters = array(
				'status' => $status
			);
			
			// Parse the results of this cURL request.
			return $this->__parse_results( $this->__request( 'PUT', 'update/' . $id, $parameters ) );
		}
		// If an error occurred.
		catch( Exception $e )
		{
			throw new Exception( $e->getMessage( ) );
		}
	}
}

?>
