TagEngine simplifies and automates the handeling of tags and tag maps in a system that requires one or more types of entities to be tagged.<br/><br/>
Use tabels.sql to create the tag engine tabels and insert test data (tag types).<br/><br/>
To utilize this class, first import TagEngine.php into your project, and require it.<br/>
This class also utilizes a MySQLi wrapper that can be found here: <a href="http://github.com/ajillion/PHP-MySQLi-Database-Class">http://github.com/ajillion/PHP-MySQLi-Database-Class</a><br/>
It can easily be modified to support the ORM of your choice, or non at all if you enjoy using the crappy mysql_connect procedural method of database access.

<pre>
<code>
require_once('Mysqlidb.php');
require_once('TagEngine.php');
</code>
</pre>

After that, create a new instance of the MySQLi wrapper and the TagEngine.<br/>
Be sure to pass the newly created database object to the TagEnigne when you instantiate it.

<pre>
<code>
$db = new Mysqlidb('host', 'username', 'password', 'databaseName');
$tagEngine = new TagEngine($db);
</code>
</pre>

<h3> Setup Methods</h3>
<p>Allows changing of the default TagEngine settings.</p>
<pre>
<code>
<?php
$tagEngine->setTables('myTagsTable', 'myTagTypesTable', 'myTagMapsTable');   // Set up the tag engine tabels
$tagEngine->setTagLen(4);   // Set the minimum number of characters a tag must have.
</code>
</pre>

<h3> mapTags Method </h3>
<p>Creates tag maps to existing tags, and adds tags that do not exist yet.</p>
<pre>
<code>
<?php
$tagEngine->mapTags(100, 'img', 'tag1,tag2,tag3');		// Create's 3 new tags and maps them to the relative id of 100 with a type of img (Image).
</code>
</pre>

<h3> getTagId Method </h3>
<p>Retrives the tag id for the given tag name.</p>
<pre>
<code>
<?php
$tagEngine->getTagId('tag3');   // Retreive the tag id for 'tag3'.
</code>
</pre>


<h3> getTagName Method </h3>
<p>Retrives the tag name of the given tag id</p>
<pre>
<code>
<?php
$tagEngine->getTagName(1);   //  Gets the tag name for tag id 1
</code>
</pre>

<h3> tagExists Method </h3>
<p>Checks to see if the given tag exists.</p>
<pre>
<code>
<?php
if ($tagEngine->tagExists('tag5'))   // If the tag exists the method returns true, if it does not it returns false.
{
	echo 'true<br/>';
}else{
	echo 'false<br/>';
}
</code>
</pre>

<h3> getTagTypeId Method </h3>
<p>Retrives the id of the given type.</p>
<pre>
<code>
<?php
$tagEngine->getTagTypeId('img');   // Gets the id for type 'img' (Image)
</code>
</pre>

<h3> getTags Method </h3>
<p>Retrives tags mapped to an entity of a particular type and returns them in a zero indexed array.</p>
<pre>
<code>
<?php
$tagEngine->getTags(100, 'img');   // Gets all tags that are mapped to relative id 100 and type img (Image).
</code>
</pre>

<h3> cleanTags Method </h3>
<p>Removes all tag maps for the given relative id and type. Also removes the tag if it is no longer mapped to any other entity.</p>
<pre>
<code>
<?php
$tagEngine->cleanTags(100, 'img');   // Removes all tag maps to relative id 100 of type 'img' (Image) and deletes the tags that were 
                                     //  mapped to it (tag1,tag2,tag3) since they are no longer in use.
</code>
</pre>

<h3> removeTagMap Method </h3>
<p>Manualy remove one tag map with the given parameters.</p>
<pre>
<code>
<?php
$tagEngine->removeTagMap(100, 'img', 1);   // Remove the tag map to relative id 100 of type 'img' (Image) where the tag id is 1
</code>
</pre>