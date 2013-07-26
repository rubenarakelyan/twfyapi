// **********************************************************************
// OpenAustralia.org API JavaScript interface
// Version 1.1
// Author: Ruben Arakelyan <ruben@ra.me.uk>
//
// Copyright (C) 2010-2011 Ruben Arakelyan.
// This file is licensed under the licence available at
// http://creativecommons.org/licenses/by-sa/3.0/
//
// For more information, see https://github.com/rubenarakelyan/twfyapi
//
// Inspiration: WebService::TWFY::API by Spiros Denaxas
// Available at http://search.cpan.org/~sden/WebService-TWFY-API-0.03/
// **********************************************************************

var OAAPI =
{

	// API key
	key: "",

	// Default constructor
	OAAPI: function(key)
	{
		// Check and set API key
		if (key == undefined || key == "")
		{
			throw "ERROR: No API key provided.";
		}
		if (!key.match(/^[A-Za-z0-9]+$/))
		{
			throw "ERROR: Invalid API key provided.";
		}
		OAAPI.key = key;

		return OAAPI;
	},

	// Send an API query
	query: function(func, args)
	{
		// Exit if any arguments are not defined
		if (func == undefined || func == "" || args == undefined || args == "" || args.constructor != Object)
		{
			throw "ERROR: Function name or arguments not provided.";
		}

		// Construct the query
		var query = new OAAPI_Request.OAAPI_Request(func, args, OAAPI.key);

		// Execute the query
		if (query.constructor == Object)
		{
			OAAPI._execute_query(query);
		}
		else
		{
			throw "ERROR: Could not assemble request using OAAPI_Request.";
		}
	},

	// Execute an API query
	_execute_query: function(query)
	{
		// Make the final URL
		var URL = query.encode_arguments();

		// Load the data into the page
		var script = document.createElement("script");
		script.setAttribute("src", URL);
		document.getElementsByTagName("head")[0].appendChild(script); 
	}

};


var OAAPI_Request =
{

	// API URL
	URL: "http://www.openaustralia.org/api/",

	// Chosen function, arguments and API key
	func: "",
	args: "",
	key: "",

	// Default constructor
	OAAPI_Request: function(func, args, key)
	{
		// Set function, arguments and API key
		OAAPI_Request.func = func;
		OAAPI_Request.args = args;
		OAAPI_Request.key = key;

		// Get and set the URL
		OAAPI_Request.URL = OAAPI_Request._get_uri_for_function(OAAPI_Request.func);

		// Check to see if valid URL has been set
		if (OAAPI_Request.URL == undefined || OAAPI_Request.URL == "")
		{
			throw "ERROR: Invalid function: " + OAAPI_Request.func + ". Please look at the documentation for supported functions.";
		}

		return OAAPI_Request;
	},

	// Encode function arguments into a URL query string
	encode_arguments: function()
	{
		// Validate the output argument if it exists
		if (OAAPI_Request.args["output"] != undefined)
		{
			if (!OAAPI_Request._validate_output_argument(OAAPI_Request.args["output"]))
			{
				throw "ERROR: Invalid output type: " + OAAPI_Request.args["output"] + ". Please look at the documentation for supported output types.";
			}
		}

		// Validate the callback argument
		if (OAAPI_Request.args["callback"] == undefined)
		{
			throw "ERROR: Callback argument must be supplied for the JavaScript interface.";
		}

		// Make sure all mandatory arguments for a particular function are present
		if (!OAAPI_Request._validate_arguments(OAAPI_Request.func, OAAPI_Request.args))
		{
			throw "ERROR: All mandatory arguments for " + OAAPI_Request.func + " not provided.";
		}

		// Assemble the URL
		var full_url = OAAPI_Request.URL + "?key=" + OAAPI_Request.key + "&";
		var arg = Object.keys(OAAPI_Request.args);
		for (var i = 0; i < arg.length; i++)
		{
			full_url += arg[i] + "=" + encodeURIComponent(OAAPI_Request.args[arg[i]]) + "&";
		}
		full_url = full_url.substring(0, full_url.length - 1);

		return full_url;
	},

	// Get the URL for a particular function
	_get_uri_for_function: function(func)
	{
		// Exit if any arguments are not defined
		if (func == undefined || func == "")
		{
			return "";
		}

		// Define valid functions
		var valid_functions = {
			"getDivisions"       : "Returns list of electoral divisions",
			"getRepresentative"  : "Returns main details for a member of the House of Representatives",
			"getRepresentatives" : "Returns list of members of the House of Representatives",
			"getSenator"         : "Returns details for a Senator",
			"getSenators"        : "Returns list of Senators",
			"getDebates"         : "Returns Debates (either House of Representatives or Senate)",
			"getHansard"         : "Returns any of the above",
			"getComments"        : "Returns comments"
		};

		// If the function exists, return its URL
		if (valid_functions[func] != undefined)
		{
			return OAAPI_Request.URL + func;
		}
		else
		{
			return "";
		}
	},

	// Validate the "output" argument
	_validate_output_argument: function(output)
	{
		if (output == undefined || output == "")
		{
			return false;
		}

		// Define valid output types
		// Note: XML, PHP and RABX are not available since they are not compatible with cross-domain JSONP
		var valid_params = {
			"js"   : "a JavaScript object"
		};

		// Check to see if the output type provided is valid
		if (valid_params[output] != undefined)
		{
			return true;
		}
		else
		{
			return false;
		}
	},

	// Validate arguments
	_validate_arguments: function(func, args)
	{
		// Define mandatory arguments
		var functions_params = {
			"getDivisions"       : [],
			"getRepresentative"  : [],
			"getRepresentatives" : [],
			"getSenator"         : ["id"],
			"getSenators"        : [],
			"getDebates"         : ["type"],
			"getHansard"         : [],
			"getComments"        : []
		};

		// Check to see if all mandatory arguments are present
		var required_params = functions_params[func];
		for (param in required_params)
		{
			if (!args.hasOwnProperty(param))
			{
				return false;
			}
		}

		return true;
	}

};

Object.keys = Object.keys || function(o)
{
	var result = [];
	for (var name in o)
	{
		if (o.hasOwnProperty(name))
		{
			result.push(name);
		}
	}
	return result;
};
