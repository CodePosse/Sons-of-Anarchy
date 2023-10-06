<?php

$table_template['render_if_no_records'] = 1;

$table_template['header']['sorting'] = 
$table_template['header']['filtering'] = 
0
;

switch
	(	$table
	):
	case 'tournament':
		$table_template['template'] = 'tournament';
	default:
//		$table_template['reorder']		=	'';
		$table_template['new_record']	=	'';
endswitch;
