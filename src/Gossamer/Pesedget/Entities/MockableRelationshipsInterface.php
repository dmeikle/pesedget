<?php

namespace Gossamer\Pesedget\Entities;

/**
 * MockableRelationshipsInterface
 *  this class is for creating a join on tables that are not normally related.
 * 
 *  It will create a 1 to 1 join on a table specified in the join.
 *  The row will be attached as a child node to the result.
 * 
 *  usage:
 * private $mockableRelationships = array(
        'ProductsI18n' => array(
            'ProductsI18n.Products_id' => 'Products_id'//this is the array key value to swap
        ),
 *      'Staff' => array( //you can also create a sub array to specify fields
            'Staff.id' => array('fields' => 'firstname, lastname, Departments_id', 'column' => 'Staff_id')           
        )
 *  );
 *
 *  this will tell the connection to query ProductsI18n table, joining on 
 *  ProductsI18n.Products_id with the current entity's Products_id column
 * 
 * @author Dave Meikle
 */
interface MockableRelationshipsInterface {
   
    public function getMockableRelationships();
    
}
