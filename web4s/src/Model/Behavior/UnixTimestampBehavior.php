<?php
declare(strict_types=1);

namespace App\Model\Behavior;

use Cake\Database\Type\DateTimeType;
use Cake\Database\TypeFactory;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\I18n\FrozenTime;
use Cake\ORM\Behavior;
use DateTimeInterface;
use RuntimeException;
use UnexpectedValueException;


class UnixTimestampBehavior extends Behavior
{

    protected $_defaultConfig = [
        'implementedFinders' => [],
        'implementedMethods' => [
            'timestamp' => 'timestamp',
            'touch' => 'touch',
        ],
        'events' => [
            'Model.beforeSave' => [
                'created' => 'new',
                'modified' => 'always',
            ],
        ],
        'refreshTimestamp' => true,
    ];

    protected $_ts;

    public function initialize(array $config): void
    {
        parent::initialize($config);
        if (isset($config['events'])) {
            $this->setConfig('events', $config['events'], false);
        }
    }


    public function handleEvent(EventInterface $event, EntityInterface $entity): bool
    {
        $eventName = $event->getName();
        $events = $this->_config['events'];

        $new = $entity->isNew() !== false;
        $refresh = $this->_config['refreshTimestamp'];

        foreach ($events[$eventName] as $field => $when) {
            if (!in_array($when, ['always', 'new', 'existing'], true)) {
                throw new UnexpectedValueException(sprintf(
                    'When should be one of "always", "new" or "existing". The passed value "%s" is invalid',
                    $when
                ));
            }
            if (
                $when === 'always' ||
                (
                    $when === 'new' &&
                    $new
                ) ||
                (
                    $when === 'existing' &&
                    !$new
                )
            ) {
                $this->_updateField($entity, $field, $refresh);
            }
        }

        return true;
    }


    public function implementedEvents(): array
    {
        return array_fill_keys(array_keys($this->_config['events']), 'handleEvent');
    }


    public function timestamp(?DateTimeInterface $ts = null, bool $refreshTimestamp = false):int
    {
        if ($ts) {
            if ($this->_config['refreshTimestamp']) {
                $this->_config['refreshTimestamp'] = false;
            }
            $this->_ts = new FrozenTime($ts);
        } elseif ($this->_ts === null || $refreshTimestamp) {
            $this->_ts = new FrozenTime();
        }
        
        return intval($this->_ts->toUnixString());
    }


    public function touch(EntityInterface $entity, string $eventName = 'Model.beforeSave'): bool
    {
        $events = $this->_config['events'];
        if (empty($events[$eventName])) {
            return false;
        }

        $return = false;
        $refresh = $this->_config['refreshTimestamp'];

        foreach ($events[$eventName] as $field => $when) {
            if (in_array($when, ['always', 'existing'], true)) {
                $return = true;
                $entity->setDirty($field, false);
                $this->_updateField($entity, $field, $refresh);
            }
        }

        return $return;
    }

    protected function _updateField(EntityInterface $entity, string $field, bool $refreshTimestamp): void
    {
        if ($entity->isDirty($field)) {
            return;
        }

        $ts = $this->timestamp(null, $refreshTimestamp);

        $columnType = $this->getTable()->getSchema()->getColumnType($field);
        if (!$columnType) {
            return;
        }

        $type = TypeFactory::build($columnType);

        if (is_int($type)) {
            throw new RuntimeException('TimestampBehavior only supports columns of type int.');
        }

        $entity->set($field, intval($ts));
    }
}



