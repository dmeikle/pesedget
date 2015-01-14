<?php

namespace Gossamer\Pesedget\Entities;

/**
 * OneToManyJoinInterface - used for mapping between 2 tables on a 1 to many join
 * 
 * usage:
 * key: namespaced path to object
 * 
 * values:
 * 1st: table column name
 * 2nd: passed param from request object (easier readability if they match names)
 * 
 * 
 * example of when:
 * IncidentTypes table and Sections
 * an incident type can be associated to multiple sections - this takes 3 tables
 * Sections
 * IncidentTypes
 * IncidentTypesSections - mapping table used by this interface
 * 
 * id   IncidentTypes_id    Sections_id
 * 
 * getIdentityColumn would return IncidentTypes_id
 *      - this would be $this->getTablename() . '_id';
 * 
 * the rest of the method is looping through an array of Sections_id values
 * passed in and inserting it into the Sections_id column of the
 * IncidentTypesSections table
 */
interface OneToManyJoinInterface
{
    
//    private $manyJoinRelationships = array(
//        'components\incidents\entities\IncidentTypeSection' => array('Sections_id' => 'Sections_id')
//    );
    
    public function getManyJoinRelationships();
    
    public function getIdentityColumn();
}