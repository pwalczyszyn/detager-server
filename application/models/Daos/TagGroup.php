<?php

class Default_Dao_TagGroup extends Zend_Db_Table_Abstract
{
    protected $_name = 'dt_tag_groups';
    
    protected $_dependentTables = array('Default_Dao_Tag');
}
