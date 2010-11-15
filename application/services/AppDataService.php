<?php
require_once APPLICATION_PATH . '/services/AbstractService.php';

class AppDataService extends AbstractService
{ 

	public function loadAppData()
	{
		$loggedInUserId = $this->getIdentityId();
		$addData = array();
		
		if ($loggedInUserId != null)
		{
			$db = Zend_Db_Table::getDefaultAdapter();
			$tagGroupsTable = new Default_Dao_TagGroup();
			$tagGroupsRows = $tagGroupsTable->fetchAll('valid is true');
			
			foreach($tagGroupsRows as $tagGroupsRow)
			{
				$tagGroup = new Default_Dto_TagGroup();
				$tagGroup->id = $tagGroupsRow['id'];
				$tagGroup->name = $tagGroupsRow['name'];
				$tagGroup->tags = new Zend_Amf_Value_Messaging_ArrayCollection();
				$tagGroup->tags->source = array();
				
				$tagsTable = new Default_Dao_Tag();
				$tagsRows = $tagsTable->fetchAll($db->quoteInto('group_id = ?', $tagGroupsRow['id']));
				
				foreach($tagsRows as $tagsRow)
				{
					$tag = new Default_Dto_Tag();
					$tag->id = $tagsRow['id'];
					$tag->name = $tagsRow['name'];
					
					array_push($tagGroup->tags->source, $tag);
				}
				
				array_push($addData, $tagGroup);
			}
		}
		
		$result = new Zend_Amf_Value_Messaging_ArrayCollection();
		$result->source = $addData;
		
		return $result;
	}
	
}