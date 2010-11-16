<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Tags Class
 *
 * @category Data Mapping
 * @package Tags
 * @author Josh Campbell <josh.campbell@teslacity.com>
 * @copyright Copyright (c) 2010, Josh Campbell
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License
 * @version 1.0
 **/
class Tags extends Model
{	
	/**
	 * Name of the tags table in the database
	 *
	 * @var string
	 */
	protected $table_tags = 'tags';
	/**
	 * Name of the tagmap types table in the database
	 *
	 * @var string
	 */
	protected $table_tag_types = 'tag_types';
	/**
	 * Name of the tagmaps table in the database
	 *
	 * @var string
	 */
	protected $table_tag_maps = 'tag_maps';
	/**
	 * The minimum number of characters a tag can have.
	 *
	 * @var integer
	 */
	protected $_tag_len = 2;
	
	/**
	 * Initialize Tag Model - Get CI instance
	 */
	public function Tags()
	{
		$this->CI =& get_instance();
	}
	
	/**
	 * Set/Change the tag tables.
	 * 
	 * @uses this->set_tables('pre_tags', 'pre_tag_types', 'pre_tag_maps')
	 *
	 * @param string $tableTags The name of the tags table.
	 * @param string $table_tag_types The name of the tag types table.
	 * @param string $table_tag_maps The name of the tag maps table.
	 */
	public function set_tables($table_tags, $table_tag_types, $table_tag_maps)
	{
		$this->table_tags		= $table_tags;
		$this->table_tag_types	= $table_tag_types;
		$this->table_tag_maps		= $table_tag_maps;
	}
	
	/**
	 * Set/Change the minimum tag length.
	 * 
	 * @uses this->set_tag_len(2)
	 *
	 * @param integer $tag_len The minimum number of characters a tag can have.
	 */
	public function set_tag_len($tag_len)
	{
		$this->_tag_len = $tag_len;
	}

	/**
	 * Get the id of a tag name.
	 * 
	 * @uses this->get_tag_id('tagName')
	 *
	 * @param string $name The tag name.
	 * @return mixed Returns the tag_id if the tag exists, false if it does not.
	 */
	public function get_tag_id($name)
	{
		$this->CI->db->select('tag_id')
			->where('name', trim($name));
		$query = $this->CI->db->get($this->table_tags, 1);
		
		if ($query->num_rows() > 0)
		{
		   $row = $query->row(); 
		   return $row->tag_id;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Does the tag name exist?
	 * 
	 * @uses this->tag_exists('tagName')
	 *
	 * @param string $name The tag name.
	 * @return boolean Returns TRUE if the tag exists, FALSE if it does not.
	 */	
	public function tag_exists($name)
	{
		($this->get_tag_id($name)) ? $result = TRUE : $result = FALSE;
		return $result;
	}

	/**
	 * Get the tag type's id.
	 * 
	 * @uses this->get_tag_type_id('type')
	 *
	 * @param string $type The 3 character tag type name. (Not to be confused with the long_name.)
	 * @return mixed Returns the type_id if the tag type exists, false if it does not.
	 */
	public function get_tag_type_id($type)
	{
		$this->CI->db->select('type_id')
			->where('name', trim($type));
		$query = $this->CI->db->get($this->table_tag_types, 1);
		
		if ($query->num_rows() > 0)
		{
		   $row = $query->row(); 
		   return $row->type_id;
		}
		else
		{
			log_message('error', 'Invalid tag type: "'.$type.'"');
			return FALSE;
		}
	}

	/**
	 * Get the tag_id/tags for an entity of the given relative id and type.
	 * 
	 * @uses this->get_tags(rel_id, type)
	 *
	 * @param integer $rel_id The id of the entity being searched.
	 * @param string $type The 3 character tag type name being searched. (Not to be confused with the long_name.)
	 * @return array Returns an array of string containing the tags where the key is the tag_id and the data is the tag name.
	 */
	public function get_tags($rel_id, $type)
	{
		$tags_arr = array();	// Temporary tags array.
		
		$type_id = $this->get_tag_type_id($type);
		
		$this->CI->db->select('tag_id')
			->where('type_id', $type_id)
			->where('rel_id', $rel_id);
		$query = $this->CI->db->get($this->table_tag_maps);
		
		foreach($query->result() as $row)
		{
			// TODO: convert to join
			$tags_arr[$row->tag_id] = htmlentities($this->get_tag_name($row->tag_id));
			//array_push($tags_arr,$this->get_tag_name($row->tag_id));
		}
		
		return $tags_arr;
	}
	
	/**
	 * Get all tag_id/tags.
	 * 
	 * @uses this->get_all_tags()
	 *
	 * @return array Returns an array of strings containing the tags where the key is the tag_id and the data is the tag name.
	 */
	public function get_all_tags()
	{
		$tags_arr = array();	// Temporary tags array.
		
		$query = $this->CI->db->get($this->table_tag_maps);
		foreach($query->result() as $row) {
			$tags_arr[$row->tag_id] = $this->get_tag_name($row->tag_id);
			//array_push($tags_arr,$this->get_tag_name($row->tag_id));
		}
		return $tags_arr;
	}
	
	
	/**
	 * Get all tag_id/tags.
	 * 
	 * @uses this->get_all_tags_cvs()
	 *
	 * @return array Returns a comma seperated string containing all tags.
	 */
	public function get_all_tags_cvs()
	{
		$tags = $this->get_all_tags();
		return implode('","', $tags);
	}
	
	
	/**
	 * Get the tag name.
	 * 
	 * @uses this->get_tag_name(tag_id)
	 *
	 * @param integer $tag_id The id of the tag to be retrived.
	 * @return string Returns the tag name of the given tag_id.
	 */	
	public function get_tag_name($tag_id)
	{
		$this->CI->db->select('name', $tag_id)
			->where('tag_id', $tag_id);
		$query = $this->CI->db->get($this->table_tags, 1);
		
		if ($query->num_rows() > 0)
		{
		   $row = $query->row(); 
		   return $row->name;
		}
		else
		{
			return FALSE;
		}
	}
	
	
	/**
	 * Adds tags to the database if they do not already exist and creates tag maps to them. 
	 * 
	 * @uses this->map_tags(105, 'img', 'tag1,tag2,tag3')
	 *
	 * @param integer $rel_id The id of the entity being tagged.
	 * @param string $type The 3 character type.
	 * @param string $tags A comma delimited string of tags to be mapped to the rel_id.
	 * @return array Returns an array containing 2 other arrays, [tag_ids] and [map_ids] each contains the id's to their respective referances for each tag/map created. 
	 */
	public function map_tags($rel_id, $type, $tags)
	{
		$new_tags_arr = array();
		$tag_ids = array();
		$map_ids = array();
		
		$tags_arr = explode(',', $tags);
		
		$type_id = $this->get_tag_type_id($type);
		
		$i = 0;
		foreach ($tags_arr as $tag) {
			$tag = trim($tag);
			if (strlen($tag) >= $this->_tag_len)							// Tag must be at least X characters.
			{
				array_push($new_tags_arr, $tag);
				
				$tag_id = $this->get_tag_id($tag);							// If the tag exists get its ID.
				if (!$tag_id) $tag_id = $this->insert_tag($tag);			// If not, create the tag and get the new id.
				array_push($tag_ids, $tag_id);
				$result = $this->add_tag_map($rel_id, $type_id, $tag_id);	// Add tag map for this tag if it does not already exist.
				array_push($map_ids, $result);
			}
			$i++;
		}
		$results = array('tags' => $new_tags_arr, 'tag_ids' => $tag_ids, 'map_ids' => $map_ids);		// Build the id arrays
		return $results;
	}

	/**
	 * Insert a tag
	 * 
	 * @uses this->insert_tag('tagname')
	 *
	 * @param string $name The tag to be inserted.
	 * @return integer Returns the id of the tag that was inserted.
	 */
	protected function insert_tag($name)
	{
		$insert_data = array('name' => $name);
		$this->CI->db->insert($this->table_tags, $insert_data);
		return $this->CI->db->insert_id();
	}

	/**
	 * Creates a map entry between a relative id, tag, and type if one does not already exist.
	 * 
	 * @uses this->add_tag_map(rel_id,type_id,tag_id)
	 *
	 * @param integer $rel_id The id of the entity being tagged.
	 * @param integer $type_id The id of the type of tag being mapped.
	 * @param integer $tag_id The id of the tag being mapped to.
	 * @return integer Returns the id of the map that was inserted or false if it exists or there was an error inserting.
	 */
	protected function add_tag_map($rel_id, $type_id, $tag_id)
	{
		$this->CI->db->select('id')
			->where('rel_id', $rel_id)
			->where('type_id', $type_id)
			->where('tag_id', $tag_id);
		$query = $this->CI->db->get($this->table_tag_maps, 1);			// Check for existing map.
		
		if ($query->num_rows() == 0)
		{
		   	$insert_data = array(
				'rel_id'  => $rel_id,
				'type_id' => $type_id,
				'tag_id'  => $tag_id);
			$result = $this->CI->db->insert($this->table_tag_maps, $insert_data);		// Add new map.
		}
		else
		{
			$row = $query->row();
			$result = $row->id;			// Get the id of existing the map.
		}
		
		return $result;
	}

	/**
	 * When deleting an entity, use this method to remove its tag maps and delete the tags mapped to it if they are no longer in use.
	 * 
	 * @uses this->clean_tags(rel_id,type)
	 *
	 * @param integer $rel_id The id of the entity being cleaned.
	 * @param integer $type_id The type of tag being cleaned.
	 */
	public function clean_tags($rel_id, $type)
	{
		$tags_arr = $this->get_tags($rel_id, $type);
		foreach($tags_arr as $tag) {							// Loop through each tag mapped to this entity and remove the mapping.
			$tag_id = $this->get_tag_id($tag);
			$this->remove_tag_map($rel_id, $type, $tag_id);
		}
	}

	/**
	 * Remove a tag map and delete the tag it was mapped to if that tag is no longer mapped to any other entity of any type.
	 * 
	 * @uses this->remove_tag_map(rel_id,type_id,tag_id)
	 *
	 * @param integer $rel_id The id of the entity map being removed.
	 * @param integer $type The 3 character string representing the the type of tag being removed.
	 * @param integer $tag_id The id of the tag being unmapped.
	 */
	public function remove_tag_map($rel_id, $type, $tag_id)
	{
		$type_id = $this->get_tag_type_id($type);
		
		$this->CI->db->where('rel_id', $rel_id)
			->where('type_id', $type_id)
			->where('tag_id', $tag_id)
			->delete($this->table_tag_maps);			// Remove the tag map.
		
		$this->CI->db->select('id')
			->where('tag_id', $tag_id);
		$query = $this->CI->db->get($this->table_tag_maps, 1);	// Is this tag in use in any other map?
		
		if ($query->num_rows() == 0)					// If the tag is no longer in use, delete it as well.
		{
			$this->remove_tag($tag_id);
		}
		
		if ($this->CI->db->affected_rows() > 0)
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Removes a tag.
	 * 
	 * @uses this->remove_tag(tag_id)
	 *
	 * @param integer $tag_id The id of the tag being removed.
	 */
	protected function remove_tag($tag_id)
	{
		$this->CI->db->where('tag_id', $tag_id)
			->delete($this->table_tags);
	}

} // END class