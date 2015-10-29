<?php

/*
 *  This file is part of the Quantum Unit Solutions development package.
 *
 *  (c) Quantum Unit Solutions <http://github.com/dmeikle/>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace tests\Gossamer\Pesedget\Sql;

use Gossamer\Pesedget\Sql\SqlBuilder;
use Gossamer\Pesedget\Sql\Expressions\Select;
use Gossamer\Pesedget\Sql\Expressions\From;
use Gossamer\Pesedget\Sql\Expressions\Where;
use Gossamer\Pesedget\Sql\Expressions\OrderBy;
use Gossamer\Pesedget\Sql\Expressions\LeftJoin;
use Gossamer\Pesedget\Sql\Expressions\JoinParam;

/**
 * SqlBuilder
 *
 * @author Dave Meikle
 */
class SqlBuilderTest extends \tests\BaseTest {

    public function testBasicSelect() {
        $builder = new SqlBuilder();
        $builder->add('select', new Select(array('firstname', 'lastname')))
                ->add('from', new From('Staff'))
                ->add('where', new Where(array('id=?1')))
                ->add('orderBy', new OrderBy('lastname, firstname', 'ASC'));

        echo $builder->toSql() . "\r\n";
    }

    public function testJoinSelect() {
        $builder = new SqlBuilder();
        $builder->add('select', new Select(array('firstname', 'lastname')))
                ->add('from', new From('Staff', 's'))
                ->add('leftJoin', new LeftJoin('StaffAuthorizations', 'sa'))
                ->add('joinParam', new JoinParam(array('sa.Staff_id = s.id', 'sa.id > 1')))
                ->add('leftJoin', new LeftJoin('ProjectManagers', 'pm'))
                ->add('joinParam', new JoinParam(array('pm.Staff_id = s.id')))
                ->add('where', new Where(array('id=?1')))
                ->add('orderBy', new OrderBy('lastname, firstname', 'ASC'));

        echo $builder->toSql() . "\r\n";
    }

    /**
     * @group where
     */
    public function testWhere() {
        $builder = new SqlBuilder();
        $builder->add('select', new Select(array('firstname', 'lastname')))
                ->add('from', new From('Staff', 's'))
                ->add('where', new Where(array('firstname = "dave"')))
                ->add('orderBy', new OrderBy('lastname, firstname', 'ASC'));

        echo $builder->toSql() . "\r\n";
    }

}
