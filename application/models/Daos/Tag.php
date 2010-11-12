<?php

class Default_Dao_Tag extends Zend_Db_Table_Abstract
{
    protected $_name = 'dt_tags';
    
    protected $_dependentTables = array('Default_Dao_BookmarkTag');
    
	protected $_referenceMap    = array(
        'tags' => array(
            'columns'           => 'group_id',
            'refTableClass'     => 'Default_Dao_TagGroup',
            'refColumns'        => 'id'
        )
	);
	
}
