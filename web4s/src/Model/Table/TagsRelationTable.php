<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;

class TagsRelationTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('tags_relation');
        $this->setPrimaryKey('id');

        $this->hasOne('Tags', [
            'className' => 'Tags',
            'foreignKey' => 'id',
            'bindingKey' => 'tag_id',
            'joinType' => 'INNER',
            'propertyName' => 'Tags'
        ]);
    }

    
}