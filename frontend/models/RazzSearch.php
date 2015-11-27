<?php

    namespace frontend\models;

    use Yii;
    use yii\data\Pagination;
    use yii\data\Sort;

    class RazzSearch extends \yii\base\Model
    {

        const RESPONDER = 1;
        const NORESPONDER = 2;

        public $category, $responder, $_t, $_search, $uid;
        public $items, $pages, $sort;
        public $pageSize = 21;
        public $isEnded = false;
        public $freshOnly = false;
        public $get_which_user_i_voted_for = null;
        public $isArchive = false;

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['responder', 'pageSize', 'uid'], 'integer'],
                [['category', 't'], 'safe'],
                [['search'], 'string', 'max' => 255],
            ];
        }

        public function getSearch()
        {
            $search = preg_replace('/ {2,}/', ' ', $this->_search);
            $search = preg_replace('/[^a-zа-яё0-9\s]+/iu', '', trim($search));

            return $search;
            $keys = explode(' ', $search);
            return $keys;
        }

        public function setSearch($in)
        {
            $this->_search = $in;
        }

        public function getT()
        {

            return $this->_t;
        }

        public function setT($in)
        {
            $this->_t = $in;

            if (is_array($this->_t) && count($this->_t) < 2) {
                if (in_array(2, $this->_t)) {
                    $this->responder = self::RESPONDER;
                }
                if (in_array(1, $this->_t)) {
                    $this->responder = self::NORESPONDER;
                }
            }
        }

        public function search()
        {
            if ( is_int($this->get_which_user_i_voted_for) ) {
                // [!] get_which_user_i_voted_for contains UID of user, who's vote we need to get

                $query = (new \yii\db\Query())
                        ->select('razz.id, ' . $this->getVotedForCondition($this->get_which_user_i_voted_for))
                        ->from('{{%razz}} razz')
                        ->leftJoin('{{%rating_total}} t', 't.nid = razz.id')
                        ->leftJoin('{{%taxonomy_index}} i', 'i.nid = razz.id AND i.model="Razz"');

            } else {
                $query = (new \yii\db\Query())
                    ->select('razz.id')
                    ->from('{{%razz}} razz')
                    ->leftJoin('{{%rating_total}} t', 't.nid = razz.id')
                    ->leftJoin('{{%taxonomy_index}} i', 'i.nid = razz.id AND i.model="Razz"');
            }

            if ($this->uid) {

                if ($this->responder == self::RESPONDER) {
                    $query->where([
                        'uid' => $this->uid,
                    ])
                        ->orWhere([
                            'responder_uid' => $this->uid,
                        ])
                        ->andWhere([
                            'not',
                            ['responder_uid' => null],
                        ])
                        ->andWhere([
                            'publish' => 1,
                            'hash' => null,
                        ]);

                } else {
                    if ($this->responder == self::NORESPONDER) {
                        $query->where([
                            'responder_uid' => $this->uid,
                            'publish' => 1,
                        ])
                            ->andWhere(['not', ['hash' => null]]);
                    } else {
                        $query->where([
                            'uid' => $this->uid,
                        ])
                            ->orWhere([
                                'responder_uid' => $this->uid,
                            ])->andWhere(['publish' => 1]);
                    }
                }
            }

            if (!$this->uid) {

                if(isset($query)) {

                    if ($this->responder == self::RESPONDER) {
                        $query->where(['not', ['responder_uid' => null]]);
                    }

                    if ($this->responder == self::NORESPONDER) {
                        $query->where(['responder_uid' => null]);
                    }

                    $query->andWhere(['hash' => null, 'publish' => 1]);
                }
            }

            if (isset($this->responder)) {
                if ($this->responder == "is not null") {
                    $query->andWhere('responder_uid IS NOT NULL');
                }
            }

            if (isset($this->isEnded)) {
                if ($this->isEnded === true) {
                    $query->andWhere('(razz.created_at + ' . Razz::DAYS . ') < ' . time());
                }
            }

            if (isset($this->freshOnly)) {
                if ($this->freshOnly === true) {
                    if(isset($query)) {
                        $query->andWhere('(razz.created_at + ' . Razz::DAYS . ') > ' . time());
                    }
                }
            }

            if ($this->search) {
                $query->andWhere('(MATCH (title,description) AGAINST (:search) OR title LIKE :search2)',
                    [':search' => $this->search, ':search2' => $this->search . '%']);
            }

            if(isset($query)) {
                $query->andFilterWhere([
                    'i.tid' => $this->category,
                ])->groupBy('razz.id');


                $countQuery = clone $query;
                $this->pages = new Pagination(['totalCount' => $countQuery->count(), 'defaultPageSize' => $this->pageSize]);
            }

            /*
             * Сортировка
             */
            $this->sort = new Sort([
                'attributes' => [
                    'votes' => [
                        'desc' => ['t.votes' => SORT_DESC],
                        'asc' => ['t.votes' => SORT_ASC],
                        'label' => 'votes',
                    ],
                    'views' => [
                        'desc' => ['razz.views' => SORT_DESC],
                        'asc' => ['razz.views' => SORT_ASC],
                        'label' => 'views',
                    ],
                    'date' => [
                        'desc' => ['razz.created_at' => SORT_DESC],
                        'asc' => ['razz.created_at' => SORT_ASC],
                        'label' => 'date',
                    ],
                    'id' => [
                        'desc' => ['razz.id' => SORT_DESC],
                        'asc' => ['razz.id' => SORT_ASC],
                        'label' => 'id',
                    ],

                ],
                'defaultOrder' => ['id' => SORT_DESC],
            ]);

            if(isset($query)) {
                $query->orderBy($this->sort->orders);
                $this->items = $query->offset($this->pages->offset)->limit($this->pages->limit)->all();
            } else {
                $this->pages = new Pagination();
            }

        }

        /**
         * Истёкшие разды
         * @param null $related_uid
         * @param null $my_uid
         */
        public function getSpoiledRazzd($related_uid = null, $my_uid = null)
        {
            $connection = Yii::$app->getDb();

            $related_uid_cond = '';
            if ($related_uid) {
                $related_uid_cond = " AND ( razz.responder_uid = $related_uid OR razz.uid = $related_uid ) ";
            }

            if (!is_null($my_uid)) {
                $query = '
                select
                 t1.id, t1.endpoint, t1.voted_for
                 from (
                        SELECT razz.id, (razz.created_at + ' . Razz::DAYS . ') endpoint , ' . $this->getVotedForCondition($my_uid) . '
                        FROM {{%razz}} razz
                        WHERE
                        publish = 1
                        AND razz.responder_uid IS NOT NULL
                        AND razz.responder_stream IS NOT NULL ' .
                    $related_uid_cond . '
                        ) t1
                where t1.endpoint < ' . time();

                $query_count = 'select
                 COUNT(t1.id)
                 from (
                        SELECT razz.id, (razz.created_at + ' . Razz::DAYS . ') endpoint , ' . $this->getVotedForCondition($my_uid) . '
                        FROM {{%razz}} razz
                        WHERE
                        publish = 1
                        AND razz.responder_uid IS NOT NULL
                        AND razz.responder_stream IS NOT NULL ' .
                    $related_uid_cond . '
                        ) t1
                where t1.endpoint < ' . time();

            } else {
                $query = '
                select * from (
                        SELECT razz.id, (razz.created_at + ' . Razz::DAYS . ') endpoint
                        FROM {{%razz}} razz
                        WHERE
                        publish = 1
                        AND razz.responder_uid IS NOT NULL
                        AND razz.responder_stream IS NOT NULL
                        ' . $related_uid_cond . '
                        ) t1
                where t1.endpoint < ' . time();

                $query_count = '
                select COUNT(id) from (
                        SELECT razz.id, (razz.created_at + ' . Razz::DAYS . ') endpoint
                        FROM {{%razz}} razz
                        WHERE
                        publish = 1
                        AND razz.responder_uid IS NOT NULL
                        AND razz.responder_stream IS NOT NULL
                        ' . $related_uid_cond . '
                        ) t1
                where t1.endpoint < ' . time();
            }

            $this->items = $connection->createCommand($query)->queryAll();
            $count = (int)$connection->createCommand($query_count)->queryOne();

            $this->pages = new Pagination(['totalCount' => $count, 'defaultPageSize' => $this->pageSize]);
        }

        public function getRazzRelated($tid, $id = null, $limit = 20, $getIfIvotedFor = true, $fresh_only = true)
        {
            $ifIvotedCondition = "";
            if ($getIfIvotedFor === true) {
                if (!is_null(Yii::$app->user->id)) {
                    $ifIvotedCondition = ", " . $this->getVotedForCondition(Yii::$app->user->id);
                }
            }

            $query =  (new \yii\db\Query())
                ->select('razz.id' . $ifIvotedCondition . ", ")
                ->from('{{%razz}} razz')
                ->innerJoin('{{%taxonomy_index}} i', 'i.nid = razz.id AND i.model="Razz"')
                ->limit($limit)
                ->where([
                    'i.tid' => $tid,
                    //'status' => 1,
                    'publish' => 1,
                    'hash' => null,
                ])
                ->andWhere(['not', ['responder_uid' => null]])
                ->andFilterWhere(['!=', 'razz.id', $id])
                ;

            if ($fresh_only === true) {
                $query->andWhere('(razz.created_at + ' . Razz::DAYS . ') > ' . time());
            }

            return $query->all();
        }

        public function getRazzVoteOnChallenges($uid = null, $limit = 20, $getIfIvotedFor = true, $fresh_only = true)
        {
            $ifIvotedCondition = "";
            if ($getIfIvotedFor === true) {
                if (!is_null(Yii::$app->user->id)) {
                    $ifIvotedCondition = ", " . $this->getVotedForCondition(Yii::$app->user->id);
                }
            }

            $query = (new \yii\db\Query())
                ->select('razz.id' . $ifIvotedCondition)
                ->from('{{%razz}} razz')
                ->where([
                    //'status' => 1,
                    'publish' => 1,
                    'hash' => null,
                ])
                ->andWhere(['not', ['responder_uid' => null]])
                ->andFilterWhere([
                    'uid' => $uid,
                ]);

            if ($fresh_only === true) {
                $query->andWhere('(razz.created_at + ' . Razz::DAYS . ') > ' . time());
            }

            return $query->orderBy([
                'created_at' => SORT_DESC,
            ])
                ->limit($limit)->all();
        }

        private function getVotedForCondition($uid)
        {
            return '(
                         select name from {{%rating_votes}} rv
                         where rid = (
                                        select rid from {{%rating_total}} rt
                                        where nid = razz.id group by nid
                                      )
                                      and uid = ' . $uid . '
                         ) voted_for';
        }

        public function getUserRazzVoteOnChallenges($uid, $limit = 20, $getIfIvotedFor = true, $fresh_only = true)
        {
            $ifIvotedCondition = "";
            if ($getIfIvotedFor === true) {
                if (!is_null(Yii::$app->user->id)) {
                    $ifIvotedCondition = ", " . $this->getVotedForCondition(Yii::$app->user->id);
                }
            }

            $query = (new \yii\db\Query())
                ->select('razz.id' . $ifIvotedCondition)
                ->from('{{%razz}} razz'  )
                ->where([
                    'uid' => $uid,
                ])
                ->orWhere([
                    'responder_uid' => $uid,
                ])
                ->andWhere([
                //'status' => 1,
                    'publish' => 1,
                    'hash' => null,
                ])
                ->andWhere([
                    'not',
                    ['responder_uid' => null],
                ])
               ;

            if ($fresh_only === true) {
                $query->andWhere('(razz.created_at + ' . Razz::DAYS . ') > ' . time());
            }

            return $query ->orderBy([
                'created_at' => SORT_DESC,
            ])
                ->limit($limit)->all();
        }

        public function getRazzRespondToChallenges($limit = 20, $show_my_ones = false)
        {
            $query = (new \yii\db\Query())
                ->select('razz.id')
                ->from('{{%razz}} razz')
                ->orderBy([
                    'created_at' => SORT_DESC,
                ])
                ->where([
                    'responder_uid' => null,
                    //'status' => 1,
                    'type' => Razz::ANYONE,
                    'publish' => 1,
                ]);

            if($show_my_ones === true){

            } else {
                $query->andWhere(['NOT', ['razz.uid' => Yii::$app->user->id]]);
            }

            return $query->limit($limit)->all();
        }

        public function getUserRazzRespondToChallenges($uid, $limit = 20)
        {
            return (new \yii\db\Query())
                ->select('razz.id')
                ->from('{{%razz}} razz')
                ->orderBy([
                    'created_at' => SORT_DESC,
                ])
                ->where([
                    'responder_uid' => $uid,
                ])
                ->andWhere(['not', ['hash' => null]])
                ->limit($limit)->all();
        }

        public function getRazzAnyone($uid, $limit = 100){

            $connection = Yii::$app->getDb();

            $query = "
                select id
                from {{%razz}}
                where uid = '$uid'

                and type = '2'
                and responder_uid IS NUll
                order by created_at desc
                limit $limit
            ";

            //and hash IS NOT NULL

            $res = $connection->createCommand($query)->queryAll();

            return $res;
        }

    }
