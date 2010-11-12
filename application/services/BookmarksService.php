<?php
require_once APPLICATION_PATH . '/services/AbstractService.php';

class BookmarksService extends AbstractService
{ 

	public function create($bookmark)
	{
		$loggedInUserId = $this->getIdentityId();
		if ($loggedInUserId != null)
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			
			try
			{
				$entity =
			    	array(
			    		'url' => strtolower($bookmark->url),
			    		'title' => $bookmark->title,
			    		'description' => $bookmark->description,
			    		'user_id' => $loggedInUserId,
			    		'entry_date' => new Zend_Db_Expr('NOW()'), 
			    		'public_access' => $bookmark->publicAccess
			    	);
				
			    $bookmarkTable = new Default_Dao_Bookmark();
				$id = $bookmarkTable->insert($entity);				

				$bTagTable = new Default_Dao_BookmarkTag();
				foreach($bookmark->tags as $tag)
				{
					$bTagTable->insert(array('bookmark_id' => $id, 'tag_id' => $tag->id));
				}
				
				$db->commit();
			}
			catch(Exception $e) 
			{
				$db->rollBack();
				error_log("Exception inserting new Bookmark: " . var_export($e->getMessage(), true), 0);
			}
		}
		
		return $id;
	}

	public function update($bookmark)
	{
		$loggedInUserId = $this->getIdentityId();
		if ($loggedInUserId != null)
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			
			try
			{
				$updates =
			    	array(
			    		'url' => strtolower($bookmark->url),
			    		'title' => $bookmark->title,
			    		'description' => $bookmark->description,
			    		'public_access' => $bookmark->publicAccess
			    	);
				
			    $bookmarkTable = new Default_Dao_Bookmark();
			    $rowsUpdatedCount = $bookmarkTable->update($updates, array(
			    	$db->quoteInto('id = ?', $bookmark->id),
			    	$db->quoteInto('user_id = ?', $loggedInUserId)
			    	));				

			    error_log(var_export('rowsUpdatedCount: ' . $rowsUpdatedCount, true), 0);
			    	
				$bTagTable = new Default_Dao_BookmarkTag();
				$bTagTable->delete($db->quoteInto('bookmark_id = ?', $bookmark->id));						
				
				$bTagTable = new Default_Dao_BookmarkTag();
				foreach($bookmark->tags as $tag)
				{
					$bTagTable->insert(array('bookmark_id' => $bookmark->id, 'tag_id' => $tag->id));
				}

//				error_log(var_export('db sql: ' . $select->assemble(), true), 0);
			    
				$db->commit();
			}
			catch(Exception $e) 
			{
				$db->rollBack();
				error_log("Exception updating a Bookmark: " . var_export($e->getMessage(), true), 0);
			}
		}
	}
	
	public function remove($bookmarkId)
	{
		$loggedInUserId = $this->getIdentityId();
		if ($loggedInUserId != null)
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$db->beginTransaction();
			
			try
			{
			    $bookmarkTable = new Default_Dao_Bookmark();
			    $rowsDeletedCount = $bookmarkTable->delete(array(
			    	$db->quoteInto('id = ?', $bookmarkId),
			    	$db->quoteInto('user_id = ?', $loggedInUserId)
			    	));				

			    if ($rowsDeletedCount == 1)
			    {
					$bTagTable = new Default_Dao_BookmarkTag();
					$bTagTable->delete($db->quoteInto('bookmark_id = ?', $bookmarkId));
			    }
				
				$db->commit();
			}
			catch(Exception $e) 
			{
				$db->rollBack();
				error_log("Exception deleting Bookmark: " . var_export($e->getMessage(), true), 0);
			}
		}
	}
	
	public function loadLatest($since)
	{
		$loggedInUserId = $this->getIdentityId();
		
		if ($loggedInUserId != null)
		{			
			$select = $this->createBookmarksSelectStatement();

			// Setting since condition
			if ($since)
    			$select->where("bmk.entry_date >= ?", $since->toString(Zend_Registry::get('SQL_DATE')));
    		// Setting public or owner condition
			$select->where("bmk.public_access IS TRUE OR bmk.user_id = ?", $loggedInUserId);
			
			$result = new Zend_Amf_Value_Messaging_ArrayCollection();
			$result->source = $this->createBookmarksResultArray($select);
			
			return $result;
		}
		return null;
	}
	
	public function searchBookmarks($searchString, $searchTagsIds)
	{
		$loggedInUserId = $this->getIdentityId();
		
		if ($loggedInUserId != null)
		{
			$select = $this->createBookmarksSelectStatement();
			
			$tagsCriteria = "";
			$len = count($searchTagsIds);
			$db = Zend_Db_Table::getDefaultAdapter();
			for ($i = 0; $i < $len; $i++)
			{
				$tagId = $searchTagsIds[$i];
				$tagsCriteria .= "$tagId" . ($i < $len - 1 ? ', ' : '');
			}
			
			$select->joinInner(array('bt' => 'dt_bookmark_tags'), 'bmk.id = bt.bookmark_id', array('tag_id'));
			$select->where("bmk.public_access IS TRUE OR bmk.user_id = ?", $loggedInUserId);
			$select->where("bt.tag_id in ($tagsCriteria)");
			
			$result = new Zend_Amf_Value_Messaging_ArrayCollection();
			$result->source = $this->createBookmarksResultArray($select);
			
			return $result;
		}
		return null;
	}
	
	protected function createBookmarksSelectStatement()
	{
		$db = Zend_Db_Table::getDefaultAdapter();
		
		$select = $db->select();
    	$select->from(array('bmk' => 'dt_bookmarks'), 
    			array(
    				'id',
    				'url',
    				'title',
    				'description', 
    				'entry_date', 
    				'public_access',
    				'user_id'
    				)
    			)
    		->joininner(array('usr' => 'dt_users'), 'bmk.user_id = usr.id', 
    			array(
    				'username'
    				)
    			)
    		->where('usr.valid is true')
    		->order('bmk.entry_date desc')
    		->limit(50);
    		
    	return $select;
	}
	
	protected function createBookmarksResultArray($select)
	{
		$result = array();

		$db = Zend_Db_Table::getDefaultAdapter();
		$bookmarks = $db->fetchAll($select);
    	foreach($bookmarks as $bmkRow)
    	{
			$tagsSelect = $db->select();
    		$tagsSelect->from(array('tag' => 'dt_tags'), 
    			array(
    				'id',
    				'name',
    				'valid'
    				)
    			)
    		->joininner(array('bt' => 'dt_bookmark_tags'), 'tag.id = bt.tag_id', 
    			array(
    				'bookmark_id'
    				)
    			)
    		->where('tag.valid IS TRUE AND bt.bookmark_id = ?', $bmkRow['id']);

    		$otherTaggersSelect = $db->select();
    		$otherTaggersSelect->from(array('usr' => 'dt_users'), array('username'))
    			->joinInner(array('bmk' => 'dt_bookmarks'), 'bmk.user_id = usr.id', array())
    			->where('bmk.public_access IS TRUE AND bmk.user_id != ?', $this->getIdentityId())
    			->where('bmk.url = ?', strtolower($bmkRow['url']));
    			
    		array_push($result, $this->createBookmarkFromRow($bmkRow, $db->fetchAll($tagsSelect), $db->fetchAll($otherTaggersSelect)));
    	}
    		
		return $result;
	}
	
	protected $tagsMap = array();
	
	protected function createBookmarkFromRow($bmkRow, $tagsRows, $otherTaggersRows)
	{
    	$bmk = new Default_Dto_Bookmark();
    	$bmk->id = $bmkRow['id'];
    	$bmk->url = $bmkRow['url'];
    	$bmk->title = $bmkRow['title'];
    	$bmk->description = $bmkRow['description'];
    	$bmk->entryDate = new DateTime($bmkRow['entry_date']);
    	$bmk->publicAccess = $bmkRow['public_access'] == 1 ? true : false;
    	$bmk->ownerUsername = $bmkRow['username'];
    	$bmk->isOwner = $bmkRow['user_id'] == $this->getIdentityId();
    	
    	$tags = array();
    	foreach($tagsRows as $tagRow)
    	{
    		
	    	if (!array_key_exists($tagRow['id'], $this->tagsMap))
	    	{
	    		$tag = new Default_Dto_Tag();
	    		$tag->id = $tagRow['id'];
	    		$tag->name = $tagRow['name'];
	    		
	    		$this->tagsMap[$tagRow['id']] = $tag;
	    	}
	    	
	    	array_push($tags, $this->tagsMap[$tagRow['id']]);
    	}
    	$bmk->tags = new Zend_Amf_Value_Messaging_ArrayCollection();
    	$bmk->tags->source = $tags;
    	
    	$otherTaggers = array();
    	foreach($otherTaggersRows as $otherTaggerRow)
    	{
    		array_push($otherTaggers, $otherTaggerRow['username']);
    	}
    	$bmk->otherTaggers = new Zend_Amf_Value_Messaging_ArrayCollection();
    	$bmk->otherTaggers->source = $otherTaggers;
    	
    	return $bmk;
	}
	
}