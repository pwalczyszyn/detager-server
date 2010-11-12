<?php

class Default_Dao_BookmarkTag extends Zend_Db_Table_Abstract
{
    protected $_name = 'dt_bookmark_tags';
    
    protected $_primary = array('bookmark_id','tag_id');
    
	protected $_referenceMap    = array(
        'Bookmark' => array(
            'columns'           => 'bookmark_id',
            'refTableClass'     => 'Default_Dao_Tag',
            'refColumns'        => 'id'
        ),
        'Tag' => array(
            'columns'           => 'tag_id',
            'refTableClass'     => 'Default_Dao_Tag',
            'refColumns'        => 'id'
        )
	);
	
}
