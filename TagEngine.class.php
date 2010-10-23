<?php
/**
 * TagEngine Class
 *
 * @category Data Mapping
 * @package TagEngine
 * @author Josh Campbell <josh.campbell@teslacity.com>
 * @copyright Copyright (c) 2010, Josh Campbell
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @version 1.0
 **/
class TagEngine
{	
	/**
	 * MySqliDb instance
	 *
	 * @var mixed
	 */
	protected $db;
	/**
	 * Name of the tags table in the database
	 *
	 * @var string
	 */
	protected $tableTags = 'sysTags';
	/**
	 * Name of the tagmap types table in the database
	 *
	 * @var string
	 */
	protected $tableTagTypes = 'sysTagTypes';
	/**
	 * Name of the tagmaps table in the database
	 *
	 * @var string
	 */
	protected $tableTagMaps = 'sysTagMaps';
	/**
	 * The minimum number of characters a tag can have.
	 *
	 * @var integer
	 */
	protected $_tagLen = 2;
	
	/**
	 * Initialize TagEngine - Load database instance
	 */
	public function __construct($db)
	{
		$this->db = $db;
		//$this->db = MySqliDb::getInstance();		// Alternate method of retrieving the db instance.
	}
	
	/**
	 * Set/Change the TagEngine tables.
	 * 
	 * @uses tagEngine->setTables('myAppTags', 'myAppTagTypes', 'myAppTagMaps')
	 *
	 * @param string $tableTags The name of the tags table.
	 * @param string $tableTagTypes The name of the tag types table.
	 * @param string $tableTagMaps The name of the tag maps table.
	 */
	public function setTables($tableTags, $tableTagTypes, $tableTagMaps)
	{
		$this->tableTags		= $tableTags;
		$this->tableTagTypes	= $tableTagTypes;
		$this->tableTagMaps		= $tableTagMaps;
	}
	
	/**
	 * Set/Change the minimum tag length.
	 * 
	 * @uses tagEngine->setTagLen(2)
	 *
	 * @param integer $tagLen The minimum number of characters a tag can have.
	 */
	public function setTagLen($tagLen)
	{
		$this->_tagLen = $tagLen;
	}

	/**
	 * Get the id of a tag name.
	 * 
	 * @uses TagEngine->getTagId('tagName')
	 *
	 * @param string $name The tag name.
	 * @return mixed Returns the tagId if the tag exists, false if it does not.
	 */
	public function getTagId($name)
	{
		$this->db->where('name',trim($name));
		$result = $this->db->query("SELECT tagId FROM $this->tableTags",1);
		
		if (!empty($result))
		{
			$tagId = $result[0]['tagId'];
			return $tagId;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Does the tag name exist?
	 * 
	 * @uses TagEngine->tagExists('tagName')
	 *
	 * @param string $name The tag name.
	 * @return boolean Returns true if the tag exists, false if it does not.
	 */	
	public function tagExists($name)
	{
		($this->getTagId($name)) ? $result = true : $result = false;
		return $result;
	}

	/**
	 * Get the tag type's id.
	 * 
	 * @uses TagEngine->getTagTypeId('type')
	 *
	 * @param string $type The 3 character tag type name. (Not to be confused with the longName.)
	 * @return mixed Returns the typeId if the tag type exists, false if it does not.
	 */
	public function getTagTypeId($type)
	{
		$this->db->where('name',trim($type));
		$result = $this->db->query("SELECT typeId FROM $this->tableTagTypes",1);
		
		if (!empty($result))
		{
			$tagId = $result[0]['typeId'];
			return $tagId;
		}
		else
		{
			trigger_error('Invalid tag type: "'.$type.'"', E_USER_ERROR);
		}
	}

	/**
	 * Get the tags for an entity of the given relative id and type.
	 * 
	 * @uses TagEngine->getTags(relId, type)
	 *
	 * @param integer $relId The id of the entity being searched.
	 * @param string $type The 3 character tag type name being searched. (Not to be confused with the longName.)
	 * @return array Returns a 0 indexed array of string containing the tags.
	 */
	public function getTags($relId, $type)
	{
		$tagsArr = array();	// Temporary tags array.
		
		$typeId = $this->getTagTypeId($type);
		
		$this->db->where('typeId', $typeId);
		$this->db->where('relId', $relId);
		$result = $this->db->query("SELECT tagId FROM $this->tableTagMaps");
		foreach($result as $row)
		{
			array_push($tagsArr,$this->getTagName($row['tagId']));
		}
		return $tagsArr;
	}

	/**
	 * Get the tag name.
	 * 
	 * @uses TagEngine->getTagName(tagId)
	 *
	 * @param integer $tagId The id of the tag to be retrived.
	 * @return string Returns the tag name of the given tagId.
	 */	
	public function getTagName($tagId)
	{
		$this->db->where('tagId', $tagId);
		$result = $this->db->query("SELECT name FROM $this->tableTags",1);
		return $result[0]['name'];
	}

	/**
	 * Adds tags to the database if they do not already exist and creates tag maps to them. 
	 * 
	 * @uses TagEngine->mapTags(105, 'img', 'tag1,tag2,tag3')
	 *
	 * @param integer $relId The id of the entity being tagged.
	 * @param string $type The 3 character type.
	 * @param string $tags A comma delimited string of tags to be mapped to the relId.
	 * @return array Returns an array containing 2 other arrays, [tagIDs] and [mapIds] each contains the id's to their respective referances for each tag/map created. 
	 */
	public function mapTags($relId, $type, $tags)
	{
		$tagIds = array();
		$mapIds = array();
		
		$tagsArr = explode(',', $tags);
		
		$typeId = $this->getTagTypeId($type);
		
		$i = 0;
		foreach ($tagsArr as $tag)
		{
			$tag = trim($tag);
			if (strlen($tag) >= $this->_tagLen)							// Tag must be at least X characters.
			{
				$tagId = $this->getTagId($tag);							// If the tag exists get its ID.
				if (!$tagId) $tagId = $this->insertTag($tag);			// If not, create the tag and get the new id.
				array_push($tagIds, $tagId);
				
				$result = $this->addTagMap($relId, $typeId, $tagId);	// Add tag map for this tag if it does not already exist.
				array_push($mapIds, $result);
			}
			$i++;
		}
		$results = array('tagIds' => $tagIds, 'mapIds' => $mapIds);		// Build the id arrays
		return $results;
	}

	/**
	 * Insert a tag
	 * 
	 * @uses TagEngine->insertTag('tagname')
	 *
	 * @param string $name The tag to be inserted.
	 * @return integer Returns the id of the tag that was inserted.
	 */
	protected function insertTag($name)
	{
		$insertData = array('name' => $name);
		return $this->db->insert($this->tableTags, $insertData);
	}

	/**
	 * Creates a map entry between a relative id, tag, and type if one does not already exist.
	 * 
	 * @uses TagEngine->addTagMap(relId,typeId,tagId)
	 *
	 * @param integer $relId The id of the entity being tagged.
	 * @param integer $typeId The id of the type of tag being mapped.
	 * @param integer $tagId The id of the tag being mapped to.
	 * @return integer Returns the id of the map that was inserted or false if it exists or there was an error inserting.
	 */
	protected function addTagMap($relId, $typeId, $tagId)
	{
		$this->db->where('relId', $relId);
		$this->db->where('typeId', $typeId);
		$this->db->where('tagId', $tagId);
		$result = $this->db->query("SELECT id FROM $this->tableTagMaps",1);		// Check for existing map.
		if (empty($result))
		{
			$insertData = array(
				'relId'  => $relId,
				'typeId' => $typeId,
				'tagId'  => $tagId);
			$result = $this->db->insert($this->tableTagMaps, $insertData);		// Add new map.
		}
		else
		{
			$result = $result[0]['id'];		// Get the id of existing the map.
		}
		return $result;
	}

	/**
	 * When deleting an entity, use this method to remove its tag maps and delete the tags mapped to it if they are no longer in use.
	 * 
	 * @uses TagEngine->cleanTags(relId,type)
	 *
	 * @param integer $relId The id of the entity being cleaned.
	 * @param integer $typeId The type of tag being cleaned.
	 */
	public function cleanTags($relId, $type)
	{
		$tagsArr = $this->getTags($relId, $type);
		foreach($tagsArr as $tag){							// Loop through each tag mapped to this entity and remove the mapping.
			$tagId = $this->getTagId($tag);
			$this->removeTagMap($relId, $type, $tagId);
		}
	}

	/**
	 * Remove a tag map and delete the tag it was mapped to if that tag is no longer mapped to any other entity of any type.
	 * 
	 * @uses TagEngine->removeTagMap(relId,typeId,tagId)
	 *
	 * @param integer $relId The id of the entity map being removed.
	 * @param integer $type The 3 character string representing the the type of tag being removed.
	 * @param integer $tagId The id of the tag being removed.
	 */
	public function removeTagMap($relId, $type, $tagId)
	{
		$typeId = $this->getTagTypeId($type);
		
		$this->db->where('relId', $relId);
		$this->db->where('typeId', $typeId);
		$this->db->where('tagId', $tagId);
		$this->db->delete($this->tableTagMaps);			// Remove the tag map.
		
		$this->db->where('tagId', $tagId);
		$results = $this->db->query("SELECT id FROM $this->tableTagMaps");
		if (empty($results))							// If the tag is no longer in use, delete it as well.
		{
			$this->removeTag($tagId);
		}
	}

	/**
	 * Removes a tag.
	 * 
	 * @uses TagEngine->removeTag(tagId)
	 *
	 * @param integer $tagId The id of the tag being removed.
	 */
	protected function removeTag($tagId)
	{
		$this->db->where('tagId', $tagId);
		$this->db->delete($this->tableTags);
	}

} // END class