<?php
namespace RubenArakelyan\TWFYAPI;

/**
 * Class OAAPI
 * @package RubenArakelyan\TWFYAPI
 */
class OAAPI
{
    private $api_key;
    private $ch;

    /**
     * Constructor
     */
    public function __construct($api_key)
    {
        // Check and set API key
        if (!$api_key) {
            return _oa_error('No API key provided.');
        }

        if (!preg_match('/^[A-Za-z0-9]+$/', $api_key)) {
            return _oa_error('Invalid API key provided.');
        }

        $this->api_key = $api_key;

        // Create a new instance of cURL
        $this->ch = curl_init();

        // Set the user agent
        // It does not provide OpenAustralia.org.au with any personal information
        // but helps them track usage of this PHP class.
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'OpenAustralia.org.au API PHP interface (+https://github.com/rubenarakelyan/twfyapi)');

        // Return the result of the query
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

        // Follow redirects
        // Needed for getBoundary as the source KML comes from http://mapit.mysociety.org
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        // Delete the instance of cURL
        curl_close($this->ch);
    }

    /**
	 * Send an API query
	 *
	 * @return string
	 */
    public function query($func, $args = [])
    {
        // Exit if any arguments are not defined
        if (!isset($func) || $func == '' || !isset($args) || $args == '' || !is_array($args)) {
            return _oa_error('Function name or arguments not provided.');
        }

        // Construct the query
        $query = new OAAPI_Request($func, $args, $this->api_key);

        // Execute the query
        if (is_object($query)) {
            return $this->_execute_query($query);
        } else {
            return _oa_error('Could not assemble request using OAAPI_Request.');
        }
    }

    /**
	 * Execute an API query
	 *
	 * @return string
	 */
    private function _execute_query($query)
    {
        // Make the final URL
        $url = $query->encode_arguments();

        // Set the URL
        curl_setopt($this->ch, CURLOPT_URL, $url);

        // Get the result
        $result = curl_exec($this->ch);

        // Find out if all is OK
        if (!$result) {
            // A problem happened with cURL
            return _oa_error('cURL error occurred: ' . curl_error($this->ch));
        } else {
            $http_code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

            if ($http_code == 404) {
                // Received a 404 error querying the API
                return _oa_error('Could not reach OA server.');
            }
            
            return $result;
        }
    }
}

/**
 * Class OAAPI_Request
 * @package RubenArakelyan\TWFYAPI
 */
class OAAPI_Request
{
    private $url = 'http://www.openaustralia.org.au/api/';
    private $func;
    private $args;

    /**
     * Constructor
     */
    public function __construct($func, $args, $api_key)
    {
        // Set function, arguments and API key
        $this->func = $func;
        $this->args = $args;
        $this->api_key = $api_key;

        // Get and set the URL
        $this->url = $this->_get_uri_for_function($this->func);

        // Check to see if valid URL has been set
        if (!isset($this->url) || $this->url == '') {
            return _oa_error('Invalid function: ' . $this->func . '. Please look at the documentation for supported functions.');
        }
    }

    /**
	 * Encode function arguments into a URL query string
	 *
	 * @return string
	 */
    public function encode_arguments()
    {
        // Validate the output argument if it exists
        if (array_key_exists('output', $this->args)) {
            if (!$this->_validate_output_argument($this->args['output'])) {
                return _oa_error('Invalid output type: ' . $this->args['output'] . '. Please look at the documentation for supported output types.');
            }
        }

        // Make sure all mandatory arguments for a particular function are present
        if (!$this->_validate_arguments($this->func, $this->args)) {
            return _oa_error('All mandatory arguments for ' . $this->func . ' not provided.');
        }

        // Assemble the URL
        $full_url = $this->url . '?key=' . $this->api_key . '&';

        foreach ($this->args as $name => $value) {
            $full_url .= $name . '=' . urlencode($value) . '&';
        }

        $full_url = substr($full_url, 0, -1);

        return $full_url;        
    }

    /**
	 * Get the URL for a particular function
	 *
	 * @return string
	 */
    private function _get_uri_for_function($func)
    {
        // Exit if any arguments are not defined
        if (!isset($func) || $func == '') {
            return '';
        }

        // Define valid functions
        $valid_functions = array(
          'getDivisions'      => 'Returns list of electoral divisions',
          'getRepresentative' => 'Returns main details for a member of the House of Representatives',
          'getRepresentatives'=> 'Returns list of members of the House of Representatives',
          'getSenator'        => 'Returns details for a Senator',
          'getSenators'       => 'Returns list of Senators',
          'getDebates'        => 'Returns Debates (either House of Representatives or Senate)',
          'getHansard'        => 'Returns any of the above',
          'getComments'       => 'Returns comments',
        );

        // If the function exists, return its URL
        if (array_key_exists($func, $valid_functions)) {
            return $this->url . $func;
        } else {
            return '';
        }
    }

    /**
	 * Validate the "output" argument
	 *
	 * @return boolean
	 */
    private function _validate_output_argument($output)
    {
        // Exit if any arguments are not defined
        if (!isset($output) || $output == '') {
            return false;
        }

        // Define valid output types
        $valid_params = array(
          'xml'  => 'XML output',
          'php'  => 'Serialized PHP',
          'js'   => 'a JavaScript object', 
          'rabx' => 'RPC over Anything But XML',
        );

        // Check to see if the output type provided is valid
        if (array_key_exists($output, $valid_params)) {
            return true;
        } else {
            return false;
        }
    }

    /**
	 * Validate arguments
	 *
	 * @return boolean
	 */
    private function _validate_arguments($func, $args)
    {
        // Define manadatory arguments
        $functions_params = array(
          'getDivisions'      => array( ),
          'getRepresentative' => array( ),
          'getRepresentatives'=> array( ),
          'getSenator'        => array( 'id' ),
          'getSenators'       => array( ),
          'getDebates'        => array( 'type' ),
          'getHansard'        => array( ),
          'getComments'       => array( ),
        );

        // Check to see if all mandatory arguments are present
        $required_params = $functions_params[$func];

        foreach ($required_params as $param) {
            if (!isset($args[$param])) {
                return false;
            }
        }

        return true;
    }

}

// Custom error handler
// This isn't a real PHP error handler as we don't want text being output to
// the browser regardless of what happens
function _oa_error($err_str)
{
    // Compile the error message
    $error_output = 'ERROR: ' . $err_str;
    
    // Log the error
    error_log($error_output);
    
    // Return an object containing a TWFY error
    $error = ['error' => $error_output];
    $error = serialize($error);
    return $error;
}
