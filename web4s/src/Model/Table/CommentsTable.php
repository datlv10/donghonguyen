<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use App\Model\Behavior\UnixTimestampBehavior;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Cake\I18n\FrozenTime;

class CommentsTable extends AppTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('comments');

        $this->setPrimaryKey('id');

        $this->addBehavior('UnixTimestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new'
                ]
            ]
        ]);

        $this->hasOne('CommentsLike', [
            'className' => 'CommentsLike',
            'foreignKey' => 'comment_id',
            'propertyName' => 'CommentsLike',
            'joinType' => 'LEFT',
        ]);        
    }

    public function queryListComments($params = [])
    {
        $table = TableRegistry::get('Comments');

        // get info params
        $field = !empty($params[FIELD]) ? $params[FIELD] : SIMPLE_INFO;
        $get_only_parent = !empty($params['get_only_parent']) ? true : false;
        $check_liked = !empty($params['check_liked']) ? true : false;

        // sort
        $sort = !empty($params[SORT]) ? $params[SORT] : [];
        $sort_field = !empty($sort[FIELD]) ? $sort[FIELD] : null;
        $sort_type = !empty($sort[SORT]) && in_array($sort[SORT], [DESC, ASC]) ? $sort[SORT] : DESC;
        
        // filter
        $filter = !empty($params[FILTER]) ? $params[FILTER] : [];
        $type_comment = !empty($filter[TYPE_COMMENT]) ? $filter[TYPE_COMMENT] : null;
        $keyword = !empty($filter[KEYWORD]) ? trim($filter[KEYWORD]) : null;
        $status = isset($filter[STATUS]) && $filter[STATUS] != '' ? intval($filter[STATUS]) : null;                
        $type = !empty($filter[TYPE]) ? $filter[TYPE] : null;
        $id = !empty($filter['id']) ? intval($filter['id']) : null;
        $foreign_id = !empty($filter['foreign_id']) ? intval($filter['foreign_id']) : null;
        $parent_id = !empty($filter['parent_id']) ? intval($filter['parent_id']) : null;
        $customer_account_id = !empty($params['customer_account_id']) ? intval($params['customer_account_id']) : null;

        switch($field){
            case FULL_INFO:
            case SIMPLE_INFO:
                $fields = ['Comments.id', 'Comments.customer_account_id', 'Comments.type', 'Comments.type_comment', 'Comments.foreign_id', 'Comments.url', 'Comments.parent_id', 'Comments.full_name', 'Comments.email', 'Comments.phone', 'Comments.content', 'Comments.images', 'Comments.number_like', 'Comments.number_reply', 'Comments.rating', 'Comments.is_admin', 'Comments.created', 'Comments.status'];
            break;

            case LIST_INFO:
                $fields = ['Comments.id', 'Comments.content'];
            break;
        }

        $sort_string = 'Comments.id DESC';
        if(!empty($params[SORT])){
            switch($sort_field){
                case 'id':
                case 'comment_id':
                    $sort_string = 'Comments.id '. $sort_type;
                break;

                case 'status':
                    $sort_string = 'Comments.status '. $sort_type .', Comments.id DESC';
                break;

                case 'number_reply':
                    $sort_string = 'Comments.number_reply '. $sort_type .', Comments.number_like DESC, Comments.id DESC';
                break;

                case 'number_like':
                    $sort_string = 'Comments.number_like '. $sort_type .', Comments.number_reply DESC, Comments.id DESC';
                break;

                case 'created':
                    $sort_string = 'Comments.created '. $sort_type .', Comments.id DESC';
                break;            
            }
        }

        // filter by conditions
        $where = ['Comments.deleted' => 0];
        $contain = [];
        if(!empty($keyword)){
            $where['Comments.search_unicode LIKE'] = '%' . Text::slug(strtolower($keyword), ' ') . '%';
        }

        if(!is_null($status)){
            $where['Comments.status'] = $status;
        }

        if(!empty($type)){
            $where['Comments.type'] = $type;
        }

        if(!empty($type_comment)){
            $where['Comments.type_comment'] = $type_comment;
        }

        if(!empty($id)){
            $where['Comments.id'] = $id;
        }

        if(!empty($foreign_id)){
            $where['Comments.foreign_id'] = $foreign_id;
        }

        if(!empty($parent_id)){
            $where['Comments.parent_id'] = $parent_id;
        }

        if($get_only_parent){
            $where['Comments.parent_id IS'] = null;
        }

        if(!empty($customer_account_id) && $check_liked) {
            $fields[] = 'CommentsLike.customer_account_id';
            $fields[] = 'CommentsLike.comment_id';
            
            $contain = [
                'CommentsLike' => function ($q) use ($customer_account_id) {
                    return $q->where([
                        'CommentsLike.customer_account_id' => $customer_account_id
                    ]);
                }, 
            ];
        }

        return $table->find()->contain($contain)->where($where)->select($fields)->group('Comments.id')->order($sort_string);
    }

    public function getDetailComment($id = null)
    {
        if(empty($id)) return [];

        $result = TableRegistry::get('Comments')->find()->where([
            'id' => $id,
            'deleted' => 0
        ])->first();

        return $result;
    } 

    public function parseDetailComment($data = [])
    {
        if(empty($data)) return [];

        $created = !empty($data['created']) ? $data['created'] : null;
        $time = $this->parseTimeComment($created);

        $customer_account_id = !empty($data['customer_account_id']) ? intval($data['customer_account_id']) : null;
        $result = [
            'id' => !empty($data['id']) ? intval($data['id']) : null,
            'customer_account_id' => $customer_account_id,
            'type' => !empty($data['type']) ? $data['type'] : null,
            'type_comment' => !empty($data['type_comment']) ? $data['type_comment'] : null,
            'foreign_id' => !empty($data['foreign_id']) ? intval($data['foreign_id']) : null,
            'url' => !empty($data['url']) ? $data['url'] : null,
            'parent_id' => !empty($data['parent_id']) ? intval($data['parent_id']) : null,
            'full_name' => !empty($data['full_name']) ? $data['full_name'] : null,
            'email' => !empty($data['email']) ? $data['email'] : null,
            'phone' => !empty($data['phone']) ? $data['phone'] : null,
            'content' => !empty($data['content']) ? $data['content'] : null,
            'images' => !empty($data['images']) ? json_decode($data['images'], true) : [],
            'number_like' => !empty($data['number_like']) ? intval($data['number_like']) : null,
            'number_reply' => !empty($data['number_reply']) ? intval($data['number_reply']) : null,
            'rating' => !empty($data['rating']) ? floatval($data['rating']) : null,
            'ip' => !empty($data['ip']) ? $data['ip'] : null,
            'is_admin' => !empty($data['is_admin']) ? 1 : 0,
            'admin_user_id' => !empty($data['admin_user_id']) ? intval($data['admin_user_id']) : null,
            'status' => !empty($data['status']) ? intval($data['status']) : 0,
            'created' => !empty($data['created']) ? $data['created'] : null,
            'time' => !empty($time['time']) ? $time['time'] : null,
            'full_time' => !empty($time['full_time']) ? $time['full_time'] : null,
            'liked' => 0
        ];

        if(!empty($data['CommentsLike'])){
            $result['liked'] = !empty($data['CommentsLike']['comment_id']) ? 1 : 0;
        }

        return $result;
    }

    public function getInfoRating($params = [])
    {
        $type = !empty($params['type']) ? $params['type'] : null;
        $foreign_id = !empty($params['foreign_id']) ? intval($params['foreign_id']) : null;

        if(empty($type) || empty($foreign_id)) return [];
        $table = TableRegistry::get('Comments');

        $where = [
            'type_comment' => RATING,
            'type' => $type,
            'foreign_id' => $foreign_id,
            'parent_id IS' => null,
            'status' => 1,
            'deleted' => 0
        ];

        // get avg and number rating
        $where_number = $where;
        $where_number['rating >'] = 0;

        $query = $table->find()->where($where_number);
        $result_number = $query->select([
            'avg_rating' => $query->func()->avg('rating'),
            'number_rating' => $query->func()->count('id')
        ])->first();

        // get number rating by value
        $where_one_star = $where;
        $where_one_star['rating'] = 1;
        $one_star = $table->find()->where($where_one_star)->count();


        $where_two_star = $where;
        $where_two_star['rating'] = 2;
        $two_star = $table->find()->where($where_two_star)->count();


        $where_three_star = $where;
        $where_three_star['rating'] = 3;
        $three_star = $table->find()->where($where_three_star)->count();

        $where_four_star = $where;
        $where_four_star['rating'] = 4;
        $four_star = $table->find()->where($where_four_star)->count();

        $where_five_star = $where;
        $where_five_star['rating'] = 5;
        $five_star = $table->find()->where($where_five_star)->count();
  

        $result = [
            'avg_rating' => !empty($result_number['avg_rating']) ? $result_number['avg_rating'] : null,
            'number_rating' => !empty($result_number['number_rating']) ? intval($result_number['number_rating']) : null,
            'one_star' => $one_star,
            'two_star' => $two_star,
            'three_star' => $three_star,
            'four_star' => $four_star,
            'five_star' => $five_star,
        ];

        return $result;
    }

    public function parseTimeComment($time = null)
    {
        $result = [
            'time' => '',
            'full_time' => ''
        ];

        if(empty($time)){
            return $result;
        }

        $time = date('Y-m-d H:i:s', $time);
        $time_input = new FrozenTime($time);
        $now = new FrozenTime();

        $interval = $now->diff($time_input);
        if (!empty($interval->format('%i'))) {
            $result['time'] = $interval->format('%i') . ' ' . __d('template', 'phut_truoc');
        }

        if (!empty($interval->format('%h'))) {
            $result['time'] = $interval->format('%h') . ' ' . __d('template', 'gio_truoc');
        }        

        if (!empty($interval->format('%d'))) {
            $result['time'] = $interval->format('%d') . ' ' . __d('template', 'ngay_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }

        if (!empty($interval->format('%m'))) {
            $result['time'] = $interval->format('%m') . ' ' . __d('template', 'thang_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }


        if (!empty($interval->format('%y'))) {
            $result['time'] = $interval->format('%y') . ' ' . __d('template', 'nam_truoc');
            $result['full_time'] = date(("d \M\O\N\T\H m, Y \A\T H:i"), strtotime($time));
        }

        if (empty($result['time'])) {
            $result['time'] = __d('template', 'vua_xong');
        }

        $result['full_time'] = str_replace('MONTH', __d('template', 'thang'), trim($result['full_time']));
        $result['full_time'] = str_replace('AT', __d('template', 'luc'), trim($result['full_time']));

        return $result;
    }

    public function parseNumberLike($number = null)
    {
        $x = round($number);
        $x_number_format = number_format($x);
        $x_array = explode(',', $x_number_format);
        $x_parts = ['k', 'm', 'b', 't'];
        $x_count_parts = count($x_array) - 1;
        $x_display = $x;
        $x_display = $x_array[0] . ((int)$x_array[1][0] !== 0 ? '.' . $x_array[1][0] : '');
        $x_display .= $x_parts[$x_count_parts - 1];
        
        return $x_display;
    }

    public function getNumberReply($parent_id = nullm, $type_comment = null)
    {
        if(empty($parent_id)) return 0;
        $where = [
            'parent_id' => $parent_id,
            'status' => 1,
            'deleted' => 0
        ];

        if(!empty($type_comment) && in_array($type_comment, [COMMENT, RATING])){
            $where['type_comment'] = $type_comment;
        }

        $number_reply = TableRegistry::get('Comments')->find()->where($where)->count();

        return !empty($number_reply) ? intval($number_reply) : 0;
    }

    public function getNumberComment($foreign_id = null, $type = null, $type_comment = null)
    {
        if(empty($foreign_id) || empty($type) || empty($type_comment) || !in_array($type_comment, [COMMENT, RATING])) return 0;

        $where = [
            'foreign_id' => $foreign_id,
            'type' => $type,
            'type_comment' => $type_comment,            
            'status' => 1,
            'deleted' => 0
        ];

        $number_comment = TableRegistry::get('Comments')->find()->where($where)->count();

        return !empty($number_comment) ? intval($number_comment) : 0;
    }

    public function getSchemaRating($foreign_id = null, $type = null)
    {
        if(empty($foreign_id) || empty($type)) return [];

        $field = ['rating', 'full_name'];

        $where = [
            'parent_id IS' => null,
            'deleted' => 0,
            'status' => 1,
            'foreign_id' => $foreign_id,
            'type_comment' => RATING,
            'type' => $type
        ];

        $schema_rating = TableRegistry::get('Comments')->find()->where($where)->select($field)->limit(2)->toArray();

        return !empty($schema_rating) ? $schema_rating : [];
    }
}