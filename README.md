# pesedget
php database entity manager

Initially intended as an Entity Manager to work within the GossamerCMS framework. This allows multiple datasources to be 
configured from a single configuration file and requested by their Key from the Entity Manager.
It abstracts the loading of the configuration with its own bootstrap so a method can call a connection from the Entity Manager at any
place in the software without worrying about whether it has been instantiated yet nor what the required credentials are for it.

The Entity Manager is a Singleton. to call it:

$manager = EntityManager::getInstance();
$connection = $manager->getConnection(); //default connection

you can specify individual connections by name:

$mysqlConnection = $manager->getConnection('mysql');
$mssqlConnection = $manager->getConnection('mssql');

the yml credentials looks like this:
--------------
database:  
----mysql:  
--------class: 'Gossamer\Pesedget\Database\DBConnection'  
--------credentials:  
-----------host: localhost  
-----------username: user_id  
-----------password: isnothere  
-----------dbName: db_name_here  
    
    #tell the EM which connection to use if no key specified          
----default: mysql
        
        
Why call it Pesedget?
--------------
The Pesedget is a group of entities in the Egyptian Pantheon. .... Group of Entities... thank you ... try the ribs...        
