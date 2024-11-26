<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class EmailTokenTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('email_token');
        $this->setPrimaryKey('id');
    }

}