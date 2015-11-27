<?php

    namespace frontend\models;

    use Yii;
    use frontend\models\Razz;

    /**
     * This is the model class for table "user_stat".
     *
     * @property integer $id
     * @property integer $uid
     * @property integer $type
     * @property double $rate
     * @property string $data
     * @property string $created_at
     *
     * @property User $u
     *
     * [!] cейчас   показатель challenges_won учитывает ТОЛЬКО рейзы, которые начал текущий юзер
     * @todo можно сделать, чтобы показатель учитывал рейзы, у которых razz.responder_uid = $this->user->id
     */
    class UserStat extends \yii\db\ActiveRecord
    {
        const expires = 3600;

        const GURU = "guru";
        const EXPERT = "expert";
        const ELITE = "elite";
        const NOVICE = "novice";
        const NEOPHYTE = "novice";
        const NEWBIE = "newbie";
        const CANDIDATE = " ";
        const BASIC_STATUS = "?";

        const RAZZ_THRESHOLD = 3;

        private $levels = [
            self::GURU => [91, 100],
            self::EXPERT => [81, 90],
            self::ELITE => [71, 80],
            self::NOVICE => [61, 70],
            self::NEWBIE => [50, 60],
            //self::NEOPHYTE => [40, 50],
            self::CANDIDATE => [0, 49],
        ];

        public $stat;
        public $user;

        private $won;
        private $draw;
        private $loose;
        private $compleeted;
        private $wonPercentage;
        private $status;
        private $total_views;
        private $total_votes;

        /**
         * @inheritdoc
         */
        public static function tableName()
        {
            return '{{%user_stat}}';
        }

        /**
         * @inheritdoc
         */
        public function rules()
        {
            return [
                [['uid', 'type', 'created_at'], 'required'],
                [['uid', 'created_at'], 'integer'],
                [['total'], 'number'],
                [['type'], 'string', 'max' => 40],
                [['data'], 'string', 'max' => 255],
            ];
        }

        /**
         * @inheritdoc
         */
        public function attributeLabels()
        {
            return [
                'id' => 'ID',
                'uid' => 'Uid',
                'type' => 'Type',
                'total' => 'Total',
                'data' => 'Data',
                'created_at' => 'Created At',
            ];
        }

        /**
         * @return \yii\db\ActiveQuery
         */
        public function getU()
        {
            return $this->hasOne(User::className(), ['id' => 'uid']);
        }

        private function getMethod($id)
        {

            $data = null;

            if (method_exists($this, $id)) {
                $data = $this->{$id}();
            }

            return $data;
        }

        public function get($id)
        {
            $this->getUserStat();
            $stat = null;

            if (!isset($this->stat[$id])) {
                $stat = Yii::createObject([
                    'class' => self::className(),
                    'uid' => $this->user->id,
                    'type' => $id,
                    'total' => $this->getMethod($id),
                    'created_at' => time(),
                ]);
                $stat->save();
            } elseif ($this->stat[$id]['created_at'] + self::expires < time()) {

                $stat = self::findOne($this->stat[$id]['id']);
                $stat->created_at = time();
                $stat->total = $this->getMethod($id);
                $stat->save();
            } else {
                return $this->stat[$id]['total'];
            }

            if ($stat) {
                return $stat->total;
            }
        }

        public function getUserStat()
        {

            if (!$this->stat) {
                $this->stat = (new \yii\db\Query())
                    ->select('*')
                    ->from('{{%user_stat}}')
                    ->indexBy('type')
                    ->where([
                        'uid' => $this->user->id,
                    ])->all();
            }

            return $this->stat;
        }

        public function getChallengesWon()
        {
            if (isset($this->won)) {
                if (!is_null($this->won)) {
                    return $this->won;
                }
            }

            $this->won = (int)$this->challenges_won();

            return $this->won;
        }

        public function getChallengesDraw()
        {
            if (isset($this->draw)) {
                if (!is_null($this->draw)) {
                    return $this->draw;
                }
            }

            $this->draw = (int)$this->challenges_draw();

            return $this->draw;
        }

        public function getChallengesLoose()
        {

            if (isset($this->loose)) {
                if (!is_null($this->loose)) {
                    return $this->loose;
                }
            }

            $this->loose = (int)$this->challenges_lose();

            return $this->loose;
        }

        public function getRazzCompleted()
        {
            if (isset($this->compleeted)) {
                if (!is_null($this->compleeted)) {
                    return $this->compleeted;
                }
            }

            $this->compleeted = (int)$this->razz_completed();

            return $this->compleeted;
        }


        public function getWonPercentage()
        {
            if (isset($this->wonPercentage)) {
                if (!is_null($this->wonPercentage)) {
                    return $this->wonPercentage;
                }
            }

            if (($this->getRazzCompleted()) == 0) {
                return -1;
            }

            $this->wonPercentage = (int) (($this->getChallengesWon() / $this->getRazzCompleted()) * 100);

            return $this->wonPercentage;
        }

        /**
         * Deprecated
         * @return int
         */
        public function _getWonPercentage()
        {
            if (isset($this->wonPercentage)) {
                if (!is_null($this->wonPercentage)) {
                    return $this->wonPercentage;
                }
            }

            if (($this->getChallengesWon() + $this->getChallengesDraw()) == 0) {
                return -1;
            }

            $this->wonPercentage = $this->getChallengesWon() * 100 / ($this->getChallengesWon() + $this->getChallengesDraw());

            return (int)$this->wonPercentage;
        }

        public function getTotalVotes()
        {
            if (isset($this->total_votes)) {
                if (!is_null($this->total_votes)) {
                    return $this->total_votes;
                }
            }

            $this->total_votes = $this->total_votes();

            return $this->total_votes;
        }

        public function getTotalViews()
        {
            if (isset($this->total_views)) {
                if (!is_null($this->total_views)) {
                    return $this->total_views;
                }
            }

            $this->total_views = $this->total_views();

            return $this->total_views;
        }

        public function getBasicStatus()
        {
            return self::BASIC_STATUS;
        }

        /**
         * @return int|null|string
         */
        public function status()
        {
            if (isset($this->status)) {
                if (!is_null($this->status)) {
                    return $this->status;
                }
            }

            $razz_ids = Razz::getRazzUserRelated($this->user->id);

            $status = self::BASIC_STATUS;

            if (isset($razz_ids)) {
                if (is_array($razz_ids)) {
                    if (sizeof($razz_ids) > self::RAZZ_THRESHOLD) {

                        $status = $this->calculateStatus();
                    }
                }
            }

            $this->status = $status;

            return $status;
        }

        private function calculateStatus()
        {
            $wonPercentage = $this->getWonPercentage();

            foreach ($this->levels as $level => $range) {
                if ($wonPercentage >= $range[0] AND $wonPercentage <= $range[1]) {
                    $this->status = $level;
                    return $level;
                }
            }

            return self::BASIC_STATUS;
        }

        /**
         * @Deprecated
         */
        private function razz_level()
        {
            return (new \yii\db\Query())
                ->select('COUNT(razz.id)')
                ->from('{{%razz}} razz')
                ->andWhere([
                    'razz.uid' => $this->user->id,
                ])->scalar();
        }

        private function total_views()
        {
            $razzModel = new Razz();
            $total = $razzModel->getViewsTotalByUserRazzd($this->user->id);

            if (isset($total['views'])) {
                return $total['views'];
            }

            return null;
        }

        /**
         * Deprecated
         *
         * @return bool|string
         */
        private function _total_views()
        {

            $query = (new \yii\db\Query())
                ->select('SUM(views)')
                ->from('{{%razz}}')
                ->where([
                    'uid' => $this->user->id,
                ]);

            return $query->scalar();
        }

        private function total_votes()
        {
            $created_at = time() - Razz::DAYS;

            $query = '
            SELECT (
                    select SUM(rt.votes) from {{%rating_total}} rt where rt.nid in(
                    select rz.id
                    FROM {{%razz}} rz   WHERE rz.uid = \'' . $this->user->id . '\'
                    )
                    and rt.name = \'my\'
                    )
                    +
                    (
                    select SUM(rt.votes) from {{%rating_total}} rt where rt.nid in(
                    select rz.id
                    FROM {{%razz}} rz   WHERE rz.responder_uid = \'' . $this->user->id . '\'
                    )
                    and rt.name = \'responder\'
                    )
            ';

            $command = Yii::$app->getDb()->createCommand($query);

            return $command->queryScalar();

            /*
            $query = (new \yii\db\Query())
                ->select('SUM(votes)')
                ->from('{{%rating_total}} rating_total')
                ->innerJoin('{{%razz}} razz', 'rating_total.nid = razz.id')
                ->where([
                    'razz.uid' => $this->user->id,
                    'rating_total.name' => 'my',
                ]);

            return $query->scalar();
            */
        }

        /**
         * Deprecated but not refactored yet
         * @return bool|string
         */
        private function challenges_draw()
        {
            $query = (new \yii\db\Query())
                ->select('COUNT(razz.id)')
                ->from('{{%razz}} razz')
                ->innerJoin('{{%rating_total}} rating_total_my',
                    'rating_total_my.nid = razz.id AND rating_total_my.name="my"')
                ->innerJoin('{{%rating_total}} rating_total_responder',
                    'rating_total_responder.nid = razz.id AND rating_total_responder.name="responder"')
                ->where('rating_total_my.rating = rating_total_responder.rating AND razz.created_at < :created_at',
                    [':created_at' => (time() - Razz::DAYS)])
                ->andWhere([
                    'razz.uid' => $this->user->id,
                ]);

            return $query->scalar();
        }

        /**
         * @return bool|string
         *
         */
        private function challenges_won()
        {
            /*
            $query = (new \yii\db\Query())
                ->select('COUNT(razz.id)')
                ->from('{{%razz}} razz')
                ->innerJoin('{{%rating_total}} rating_total_my',
                    'rating_total_my.nid = razz.id AND rating_total_my.name="my"')
                ->innerJoin('{{%rating_total}} rating_total_responder',
                    'rating_total_responder.nid = razz.id AND rating_total_responder.name="responder"')
                ->where('rating_total_my.rating > rating_total_responder.rating AND razz.created_at < :created_at',
                    [':created_at' => (time() - Razz::DAYS)])
                ->andWhere([
                    'razz.uid' => $this->user->id,
                ]);

            return $query->scalar();
            */

            $created_at = time() - Razz::DAYS;
/*
            $query = 'select
                      (
                       SELECT count(t2.rating)
                            FROM (
                              SELECT
                                t.rating,
                                t.name,
                                t.nid
                              FROM {{%rating_total}} t
                        WHERE nid IN (
                        SELECT rz.id
                        FROM sf_razz rz
                        WHERE rz.uid = \'' . $this->user->id . '\' AND rz.created_at < ' . $created_at .
                ')
                        ORDER BY t.rating
                        DESC
                        LIMIT 1
                        ) t2
                        WHERE t2.name = \'my\'
                      )
                      +
                      (
                      SELECT count(t4.rating)
                      FROM (
                             SELECT
                               t3.rating,
                               t3.name,
                               t3.nid
                             FROM {{%rating_total}} t3
                             WHERE nid IN (
                               SELECT rz.id
                               FROM sf_razz rz
                               WHERE rz.responder_uid = \'' . $this->user->id . '\' AND rz.created_at < ' . $created_at .
                ')
                             ORDER BY t3.rating
                               DESC
                             LIMIT 1
                           ) t4
                      WHERE t4.name = \'responder\'
                      )';
*/

            /** Можно добавить  AND ended = 1 */

            $query = '
                select (
                    select COUNT(id)
                    from (
                        select id from (
                            SELECT rz.id,
                                (
                                    select rating
                                    from {{%rating_total}} rt
                                    where rt.nid = rz.id and rt.name = \'responder\'
                                ) responder_rating,
                                (
                                    select rating
                                    from {{%rating_total}} rt
                                    where rt.nid = rz.id and rt.name = \'my\'
                                ) my_rating
                            FROM {{%razz}} rz  WHERE rz.uid = \'' . $this->user->id . '\' AND rz.created_at < ' . $created_at . ' AND responder_stream IS NOT NULL AND responder_uid IS NOT NULL AND ended = 1
                        ) ratings
                        where responder_rating < my_rating OR responder_rating is null
                        ) ids
                    )
                    +
                    (
                    select COUNT(id)
                    from (
                        select id from (
                            SELECT rz.id,
                                (
                                    select rating
                                    from {{%rating_total}} rt
                                    where rt.nid = rz.id and rt.name = \'responder\'
                                ) responder_rating,
                                (
                                    select rating
                                    from {{%rating_total}} rt
                                    where rt.nid = rz.id and rt.name = \'my\'
                                ) my_rating
                            FROM {{%razz}} rz  WHERE rz.responder_uid = \'' . $this->user->id . '\' AND rz.created_at < ' . $created_at . ' AND responder_stream IS NOT NULL AND responder_uid IS NOT NULL AND ended = 1
                        ) ratings
                        where responder_rating > my_rating OR my_rating is null
                        ) ids
                )
            ';

            $command = Yii::$app->getDb()->createCommand($query);

            return $command->queryScalar();
        }

        private function challenges_lose()
        {
            /*
            $query = (new \yii\db\Query())
                ->select('COUNT(razz.id)')
                ->from('{{%razz}} razz')
                ->innerJoin('{{%rating_total}} rating_total_my',
                    'rating_total_my.nid = razz.id AND rating_total_my.name="my"')
                ->innerJoin('{{%rating_total}} rating_total_responder',
                    'rating_total_responder.nid = razz.id AND rating_total_responder.name="responder"')
                ->where('rating_total_my.rating < rating_total_responder.rating AND razz.created_at < :created_at',
                    [':created_at' => (time() - Razz::DAYS)])
                ->andWhere([
                    'razz.uid' => $this->user->id,
                ]);

            return $query->scalar();
            */

            $created_at = time() - Razz::DAYS;

            $query = '
                select (
                    select COUNT(id)
                    from (
                        select id from (
                            SELECT rz.id,
                                (
                                    select rating
                                    from {{%rating_total}} rt
                                    where rt.nid = rz.id and rt.name = \'responder\'
                                ) responder_rating,
                                (
                                    select rating
                                    from {{%rating_total}} rt
                                    where rt.nid = rz.id and rt.name = \'my\'
                                ) my_rating
                            FROM {{%razz}} rz   WHERE rz.uid = \'' . $this->user->id . '\' AND rz.created_at < ' . $created_at . ' AND responder_stream IS NOT NULL AND responder_uid IS NOT NULL AND ended = 1
                        ) ratings
                        where responder_rating > my_rating OR my_rating is null
                        ) ids
                    )
                    +
                    (
                    select COUNT(id)
                    from (
                        select id from (
                            SELECT rz.id,
                                (
                                    select rating
                                    from {{%rating_total}} rt
                                    where rt.nid = rz.id and rt.name = \'responder\'
                                ) responder_rating,
                                (
                                    select rating
                                    from {{%rating_total}} rt
                                    where rt.nid = rz.id and rt.name = \'my\'
                                ) my_rating
                            FROM {{%razz}} rz   WHERE rz.responder_uid = \'' . $this->user->id . '\' AND rz.created_at < ' . $created_at . ' AND responder_stream IS NOT NULL AND responder_uid IS NOT NULL AND ended = 1
                        ) ratings
                        where responder_rating < my_rating OR responder_rating is null
                        ) ids
                )
            ';

            $command = Yii::$app->getDb()->createCommand($query);

            return $command->queryScalar();
        }

        /**
         * @return int
         */
        private function razz_completed(){

            $model = new RazzSearch();
            $model->isArchive = true;
            $model->getSpoiledRazzd($this->user->id);

            if(is_array($model->items))
                return (int)sizeof($model->items);

            return 0;
        }

        /**
         * Количество истекших раздов, в которых участвовал uid
         * @return bool|string
         * Deprecated
         */
        private function _razz_completed()
        {
            /*
            $query =
                (new \yii\db\Query())
                    ->select('COUNT(razz.id)')
                    ->from('{{%razz}} razz')
                    ->where('razz.created_at < :created_at', [':created_at' => (time() - Razz::DAYS)])
                    ->andWhere([
                        'razz.uid' => $this->user->id,
                        'razz.ended' => true,
                    ]);

            return $query->scalar();
            */

            $created_at = time() - Razz::DAYS;

            $query = 'SELECT COUNT(r.id)
                        FROM {{%razz}} r
                        WHERE r.created_at < '.$created_at.
                       ' AND (r.uid = \''. $this->user->id.'\' OR r.responder_uid = \''.$this->user->id.'\')
                         AND r.ended = \'1\'
            ';

            $command = Yii::$app->getDb()->createCommand($query);

            return $command->queryScalar();
        }

    }
