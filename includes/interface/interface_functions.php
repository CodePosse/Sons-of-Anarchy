<?php

function array_to_xml
(	array $arr
,	SimpleXMLElement $xml
,	$uk	= ''
)
{   foreach
		(	$arr	as	$k	=>	$v
		):
		if	(	is_numeric
    			(	$k	
   				)
   			):
   			$ck = 
   			(	$uk	==	'leaderboard'
   			)	
   			?	'player'
   			:	substr
    			(	$uk
    			,	0
    			,	-1
    			)
    		;
    		$v['rank'] = $k + 1;
    	else:
    		$ck = $k;
    	endif;
		if	(	is_array
				(	$v
				)
			):
        	array_to_xml
        	(	$v
        	,	$xml->addChild
        		(	$ck
        		)
        	,	$ck
        	);
        else:
       		$xml->addChild
       		(	$ck
       		,	$v
       		);
       	endif;
    endforeach;
    return $xml;
}

function check_required_vars
(	$args	=	array()
)
{	extract
	(	debug::function_argument_verify
		(	array
			(	'function'			=>	__CLASS__.'->'.__FUNCTION__
			,	'arguments_input'		=>	$args
			,	'arguments_descriptions'	=>	array
				(	'expected_vars'				=>	array
					(	'default_value'				=>	array()
					)
				)
			)
		)
	);
	
	$errors = array();
	
	foreach
		(	$expected_vars	as	$var_name
		):
		if	(	!isset
				(	$GLOBALS['page']->request[$var_name]
				)
			||	(	is_scalar
					(	$GLOBALS['page']->request[$var_name]
					)
				&&	!strlen
					(	$GLOBALS['page']->request[$var_name]
					)
				)
			):
			$errors[] = 'Missing required input variable: $'
			.	$var_name
			;
		endif;
	endforeach;

	if	(	empty
			(	$errors
			)
		):
		return false;
	else:
		return $errors;
	endif;
}
	
function super_initialize_tables
(	$args	=	array()
)
{	extract
	(	debug::function_argument_verify
		(	array
			(	'function'			=>	__CLASS__.'->'.__FUNCTION__
			,	'arguments_input'		=>	$args
			,	'arguments_descriptions'	=>	array
				(	'tables'				=>	array
					(	'default_value'				=>	array()
					)
				)
			)
		)
	);
	
	foreach
		(	$tables	as	$table
		):
		$GLOBALS['dbi']->tables[$table]->initialize();
		$GLOBALS['dbi']->tables[$table]->get_ownerships();
	endforeach;	
}

function curlit
(	$opts
)
{	if	(	!empty
			(	$opts
			)
		):
		$cons = get_defined_constants
		(	true	//	categorize 'em
		);
		set_time_limit
		(	600
		);
		$ch = curl_init();
		// SET DEFAULT OPTION VALUES HERE
		$setopts = array
		(	$cons['curl']['CURLOPT_URL']			=>	''
		,	$cons['curl']['CURLOPT_HEADER']			=>	0	// set false to eliminate header info from response
		,	$cons['curl']['CURLOPT_RETURNTRANSFER']	=>	1 // Returns response data instead of TRUE(1)
	    );
		if	(	is_array
				(	$opts
				)
			):
			foreach
				(	$opts	as	$opt_name	=>	$opt_val
				):
				if	(	strstr
						(	$opt_name
						,	'CURLOPT_'
						)	==	$opt_name
					&&	isset
						(	$cons['curl'][$opt_name]
						)	
					):
					$setopts[$cons['curl'][$opt_name]] = $opt_val;
				endif;
			endforeach;
		else:
			$setopts[$cons['curl']['CURLOPT_URL']] = $opts;
		endif;
	    curl_setopt_array
	    (	$ch
	    ,	$setopts
	    );
		$result = curl_exec($ch); //execute post and get results
		curl_close
		(	$ch
		);
		return $result;
	else:
		return false;
	endif;
}

class sxe {
	function __construct
	(	$xml
	)
	{	// import / convert source xml to object
		$this->content = simplexml_load_string
		(	$xml
		);
		if	(	!$this->content
			):
			// what to do if xml could not be read from source file?
			
			// seriously, what to do?
			
			print '<h1>NO (GOOD) XML!</h1>';
			print 'Tried: "'
			.	$xml
			.	'"'
			;
			
			return false;
			
		endif;
		
		// convert obj to simpleXML element
		$this->data = new SimpleXMLElement
		(	$this->content->asXML()
		);
		
		$this->namespaces = $this->data->getNamespaces
		(	true
		);

	}
}
