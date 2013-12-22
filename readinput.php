<?php

function readinputfile()
{
$fp = fopen("input\\input.txt", 'r') or die("Could not create file!");
while (!feof($fp)){
	$content = fgetcsv($fp,null,";");
	$id = $content[0];
	$text = $content[1];
	echo $id,$text;
}
}
?>