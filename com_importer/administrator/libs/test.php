<?php

include_once 'cvsimporter.php';

$ci = new cvsimporter();

$xml		= file_get_contents('task.xml');
$cvs_data	= file_get_contents('export.csv');

$ci->setup( $xml );
//$ci->show();
$log = $ci->run( $cvs_data );
print ( implode("<br/>",$log) );

// SELECT COLUMN_NAME
// FROM INFORMATION_SCHEMA.COLUMNS 
// WHERE TABLE_SCHEMA = 'joomla25' AND TABLE_NAME ='j25_extensions';