# twfyapi

PHP, ASP.NET and JavaScript wrappers for the TheyWorkForYou.com and OpenAustralia.org.au APIs.

## Usage

    // Include the API binding
    require_once 'twfyapi.php';
    
    // Set up a new instance of the API binding
    $twfyapi = new TWFYAPI('[API KEY HERE]');
    
    // Get a list of Labour MPs in XML format
    $mps = $twfyapi->query('getMPs', array('output' => 'xml', 'party' => 'labour'));
    
    // Print out the list
    header('Content-type: application/xml');
    echo $mps;

## Options

`void TWFYAPI ( string $api_key )`

* `$api_key`: Your unique API key, obtained from TheyWorkForYou.com or OpenAustralia.org.au.

`mixed query ( string $func [, array $args = array() ] )`

* `$func`: The API function to execute.
* `$args`: (Optional) Any data to pass to the API function, as an array of keys and values.

See http://www.theyworkforyou.com/api/ and http://www.openaustralia.org.au/api/ for details of available functions and arguments.

## Error messages

* No API key provided: No API key was provided to the constructor.
* Invalid API key provided: The API key provided does not meet the expected format.
* Function name or arguments not provided: Either or both of the function and/or arguments were provided to the `query` method.
* Could not assemble request using TWFYAPI_Request: A code error occurred while attempting to construct the request to send.
* cURL error occurred: [error message]: There was a problem when trying to contact the site; the error message will provide more details.
* Could not reach [TWFY|OA] server: A 404 error was encountered when attempting to contact the site.
* Invalid function: [function name]. Please look at the documentation for supported functions: The function provided is not recognised as valid.
* Invalid output type: [output argument]. Please look at the documentation for supported output types: The `output` argument provided is not recognised as valid.
* All mandatory arguments for [function name] not provided: One or more mandatory arguments for the selected function were not provided.

## Support

Please submit issues to https://github.com/rubenarakelyan/twfyapi/issues.

## Contributing

All pull requests are gratefully accepted.

## Licence

All files in this repository are licenced under the Creative Commons Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0) licence.

See http://creativecommons.org/licenses/by-sa/3.0/ for the full licence text.

Please note that data pulled by the API is licenced separately.