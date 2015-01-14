<?php

namespace Gossamer\Pesedget\Entities;



interface OneToOneJoinInterface
{
    /** sample usage:
     * 
     * private $joinRelationships = array(
        'components\contacts\entities\Contact' => array('EventsContacts.Contacts_id', 'Contacts.id')
    );
     * EntityName = > JoinTable.Entity_Identifier, EntityTable.id
     */
    public function getJoinRelationships();
    
}