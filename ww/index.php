<?php

$wherer = explode
(	'/'
,	$_SERVER['PHP_SELF']
);
while
	(	$where	=	array_pop
		(	$wherer
		)
	):
	if	(	$where	!=	'index.php'
		):
		break;
	endif;
endwhile;

setcookie
(	'SOACO'
,	$where
,	(	time()
	+	(	60
		*	60
		*	24
		*	365
		)
	)
,	'/'
);

header
(	'Location: ../index.php'
);