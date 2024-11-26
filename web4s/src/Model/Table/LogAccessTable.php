<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Core\Exception\Exception;
use Cake\Datasource\ConnectionManager;
use Cake\Cache\Cache;

class LogAccessTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('log_access');

        $this->setPrimaryKey('session_id');
    }

    public function updateLogAccess()
    {
        $time_current = time();
        $date_current = date('Y-m-d');
        $session_id = session_id();
        $session_access = !empty($_SESSION['log_access']) ? $_SESSION['log_access'] : [];

        if (empty($session_id)) return true;

        // khi không tồn tại session log_access hoặc đã tồn tại nhưng tại thời điểm trước đó 30' thì cập nhật bản ghi log_access và counter
        $save_count = true;
        if(!empty($session_access['session_id']) && !empty($session_access['time_access']) && $session_access['session_id'] == $session_id && $time_current - $session_access['time_access'] < 1800)  $save_count = false;
        
        // ghi lại session
        $_SESSION['log_access'] =  [
            'time_access' => $time_current,
            'session_id' => $session_id
        ];
        
        // nếu không cập nhật bản ghi log_access thì ngừng tại đây
        if(!$save_count) return true;

        // xóa bản ghi cũ của log_access
        $cache_date_access = Cache::read('date_log_access');        
        if(empty($cache_date_access) || $cache_date_access != $date_current) {
            $this->deleteAll(['time_access <' => strtotime(date('Y-m-d 00:00:00'))]);
            Cache::write('date_log_access', $date_current);
        }
        
        //cập nhật bảng log_access
        $log_access_info = $this->find()->where(['session_id' => $session_id])->first();    
        if(empty($log_access_info)){
            $entity_log_access = $this->newEntity([
                'session_id' => $session_id,
                'time_access' => $time_current
            ]);
        }else{
            $entity_log_access = $this->patchEntity($log_access_info, [
                'time_access' => $time_current
            ]);
        }

        // cập nhật bảng counter        
        $counter_table = TableRegistry::get('Counters');

        $counter_info = $counter_table->find()->where(['date' => $date_current])->first();
        if(empty($counter_info)) {
            $entity_counter = $counter_table->newEntity([
                'counts' => 1,
                'date' => $date_current
            ]);
        } else {
            $entity_counter = $counter_table->patchEntity($counter_info, [
                'counts' => intval($counter_info['counts']) + 1
            ]);
        }

        $conn = ConnectionManager::get('default');
        try{
            $conn->begin();
            $save_log_access = $this->save($entity_log_access);
            if (empty($save_log_access->session_id)){
                throw new Exception();
            }            

            $save_counter = $counter_table->save($entity_counter);
            if (empty($save_counter->id)){
                throw new Exception();
            }

            $conn->commit();            

            return true;
        }catch (Exception $e) {
            return false;
        }
    }

    // lấy những session truy cập trong khoảng 30'
    public function getCounterOnline()
    {        
        return $this->find()->where(['time_access >=' => time() - 1800])->select(['id'])->count();
    }
}