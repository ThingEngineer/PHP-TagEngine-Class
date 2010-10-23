<?php
require_once('MysqliDb.php');		//This class can be downloaded here: http://github.com/ajillion/PHP-MySQLi-Database-Class
require_once('TagEngine.php');

$db = new MysqliDb('localhost', 'root', 'root', 'db');

?>
<!DOCTYPE html>

<html lang="en">
<head>
   <meta charset="utf-8">
   <title>PHP TagEngine</title>
   <meta name="author" content="Josh Campbell - Ajillion">
</head>
<body>

<p>Use tabels.sql to create the tag engine tabels and insert test data (tag types).</p>
	
<?php

$tagEngine = new TagEngine($db);
echo '<br/><strong>Tag Engine Tests</strong><br/><br/>';

echo '<strong>getTagTypeId(\'img\') =</strong> '.$tagEngine->getTagTypeId('img').'<br/><br/>';

echo '<pre>Insert 3 new tags (tag1,tag2,tag3) and map them to relId 100 with a type of img.'."\n<strong>mapTags(100,'img','tag1,tag2,tag3') =</strong>\n";
print_r ($tagEngine->mapTags(100, 'img', 'tag1,tag2,tag3'));
echo '</pre><br/>';

echo '<strong>getTagId(\'tag3\') =</strong> '.$tagEngine->getTagId('tag3').'<br/>';
echo '<strong>tagExists(\'tag3\') =</strong> ';
if ($tagEngine->tagExists('tag3'))
{
	echo 'true<br/>';
}else{
	echo 'false<br/>';
}

echo '<strong>tagExists(\'tag5\') =</strong> ';
if ($tagEngine->tagExists('tag5'))
{
	echo 'true<br/>';
}else{
	echo 'false<br/>';
}

echo '<br/><pre>Get all tagMaps with a relId of 100 and a type of img.'."\n<strong>getTags(100,'img') =</strong>\n";
print_r ($tagEngine->getTags(100, 'img'));
echo '</pre>';

echo '<br/>Remove all tagmaps with a relId of 100 and a type of img, and delete the tags if they are not used by any other entity.<br/>'."<strong>cleanTags(100,'img')</strong><br/>";
$tagEngine->cleanTags(100, 'img');

echo '<br/><pre>Get all tagMaps with a relId of 100 and a type of img.'."\n<strong>getTags(100,'img') =</strong>\n";
print_r ($tagEngine->getTags(100, 'img'));
echo '</pre>';

?>

</body>
</html>