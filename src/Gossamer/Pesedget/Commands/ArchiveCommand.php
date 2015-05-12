<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 * 
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Gossamer\Pesedget\Commands;

/**
 * ArchiveCommand
 *
 * @author Dave Meikle
 */
class ArchiveCommand extends AbstractCommand{
    
    public function execute($params = array()) {
        //first ensure the archive table exists
        
        
        //now find any rows that qualify for archiving. Let's do a nibble
        //method (1 row at a time) so we don't lock up the DB for other
        //users because someone wrote a BFQ.
        
    }
    
    private function createTable() {
        //$query = 
    }

 

}
