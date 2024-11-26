<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;

class CountersTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('counters');
        $this->setPrimaryKey('id');
    }

    public function getCounterDay()
    {
        $result = $this->find()->where([
            'date' => date('Y-m-d')
        ])->select(['counts'])->first();

        return !empty($result['counts']) ? intval($result['counts']) : 0;
    }

    public function getCounterWeek()
    {
        $date = date('Y-m-d');
        $weekday = strtolower(date('l'));

        switch($weekday) {
            case 'monday':
                $date = date('Y-m-d', (strtotime($date)));
                break;
            case 'tuesday':
                $date = date('Y-m-d', (strtotime('-1 day', strtotime($date))));
                break;
            case 'wednesday':
                $date = date('Y-m-d', (strtotime('-2 days', strtotime($date))));
                break;
            case 'thursday':
                $date = date('Y-m-d', (strtotime('-3 days', strtotime($date))));
                break;
            case 'friday':
                $date = date('Y-m-d', (strtotime('-4 days', strtotime($date))));
                break;
            case 'saturday':
                $date = date('Y-m-d', (strtotime('-5 days', strtotime($date))));
                break;
            default:
                $date = date('Y-m-d', (strtotime('-6 days', strtotime($date))));
                break;
        }

        $query = $this->find()->where([
            'date >=' => $date
        ]);

        $result = $query->select(['total_sum' => $query->func()->sum('Counters.counts')])->first();

        return !empty($result['total_sum']) ? intval($result['total_sum']) : 0;
    }

    public function getCounterMonth()
    {
        $query = $this->find()->where([
            'date >=' => date('Y-m') . "-01"
        ]);

        $result = $query->select(['total_sum' => $query->func()->sum('Counters.counts')])->first();
        return !empty($result['total_sum']) ? intval( $result['total_sum']) : 0;        
    }

    public function getCounterAll()
    {
        $query = TableRegistry::get('Counters')->find();
        $result = $query->select(['total_sum' => $query->func()->sum('Counters.counts')])->first();

        return !empty($result['total_sum']) ? intval($result['total_sum']) : 0;
    }
}