<?php

if	(	!empty
		(	$errors
		)	
	):
	if	(	empty
			(	$response
			)
		):
		$response = array();
	elseif
		(	!is_array
			(	$response
			)
		):
		$response = array
		(	'success'	=>	$response
		);
	endif;
	$response['errors'] = $errors;
	unset
	(	$errors
	);
endif;

if	(	!empty
		(	$response
		)
	):
	switch
		(	$response_format
		):
		case 'none';
			break;
		case 'html':
			header
			(	'Content-Type:text/html'
			);
		case 'raw':
			if	(	is_object
					(	$response	
					)
				):
				if	(	!empty
						(	$response->header['x-aws-requestbody']
						)
					):
					$response->header['x-aws-requestbody'] = '[TRUNCATED BY PHP]';
				endif;
				var_dump
				(	$response
				);
			elseif
				(	is_array
					(	$response
					)
				):
				var_dump
				(	$response
				);
			else:	
				echo $response;
			endif;
			break;
//		case 'jsonpre':		
		case 'jsonp':
		case 'json':
			header
			(	'Content-Type:text/json'
			);  
			header
			(	'Content-Type:application/json'
			);
			$response = preg_replace
			(	'/\\\\+\//'
			,	'/'
			,	json_encode
				(	array
					(	$GLOBALS['page']->request['z']	=>	$request_attributes
					,	'response'						=>	$response
					)
				)
			);
			echo
			(	$response_format	==	'jsonp'
			&&	!empty
				(	$GLOBALS['page']->request['callback']
				)
			)
			?	$GLOBALS['page']->request['callback']
				.	'('
				.	$response
				.	')'
			:	$response
			;
			break;
		case 'string':
			header
			(	'Content-Type:text/plain'
			);
			$ptxt = '';
			foreach
				(	$response	as	$k	=>	$v
				):
				if	(	!empty
						(	$ptxt
						)
					):
					$ptxt .= '&';
				endif;
				$ptxt .= $k
				.	'='
				.	rawurlencode
					(	$v
					)
				;
			endforeach;
			echo $ptxt;
			unset
			(	$response
			,	$ptxt	
			);
			break;
		case 'array_to_xml':
			$response = array_to_xml
			(	$response
			,	new SimpleXMLElement
				(	'<root/>'
				)
			)->asXML()
			;
		case 'xml':
		default:
			header
			(	'Content-Type:text/xml'
			);  
			header
			(	'Content-Type:application/xml'
			);  		
			echo $response;
			break;
	endswitch;
endif;
