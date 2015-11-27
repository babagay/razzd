<?php

namespace frontend\widgets\rating\models;

use Yii;

//use yii\data\SqlDataProvider;

/**
 * This is the model class for table "comments".
 *
 * @property integer $id
 * @property integer $uid
 * @property string $comment
 */
class Rating extends \yii\base\Model {

    public $vote, $nid, $model, $return_id;
    //razzd Only
    public $return;
    public $uid;

    public function init() {

        $this->uid = $this->uid ? $this->uid : Yii::$app->user->id;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['vote', 'nid', 'model'], 'required'],
            [['nid', 'uid'], 'integer'],
            [['model', 'return_id'], 'string', 'max' => 50],
            [['vote'], 'safe']
        ];
    }

    public function isRated() {
        $rid = Yii::$app->db->createCommand('SELECT raiting.id FROM {{%raiting}} raiting '
                        . ' INNER JOIN {{%rating_votes}} rating_votes ON rating_votes.rid = raiting.id '
                        . 'WHERE nid=:nid AND model=:model AND uid=:uid LIMIT 1', [':nid' => $this->nid, ':model' => $this->model, ':uid' => $this->uid])->queryScalar();

        if (!$rid)
            return true;


        return false;
    }

    public function loadData($rates) {

        $rows = [];
        if ($this->uid) {
            $rates = array_shift($rates);
            $names = array_keys($rates);

            $rows = (new \yii\db\Query())
                    ->select('rating_votes.id,rating_votes.name,rating_votes.vote')
                    ->from('{{%rating_votes}} rating_votes')
                    ->innerJoin('{{%raiting}} raiting', 'raiting.id = rating_votes.rid')
                    ->where([
                        'nid' => $this->nid,
                        'model' => $this->model,
                        'uid' => $this->uid,
                        'name' => $names,
                    ])
                    ->all();
        } else {

            $rates = array_shift($rates);
            $names = array_keys($rates);

            $rows = (new \yii\db\Query())
                    ->select('rating_votes.id,rating_votes.name,rating_votes.vote')
                    ->from('{{%rating_votes}} rating_votes')
                    ->innerJoin('{{%raiting}} raiting', 'raiting.id = rating_votes.rid')
                    ->where([
                        'nid' => $this->nid,
                        'model' => $this->model,
                        'ip' => $_SERVER['REMOTE_ADDR'],
                        'name' => $names,
                    ])
                    ->all();
        }

        if ($rows) {
            foreach ($rows as $vote) {
                $this->vote[$vote['name']] = $vote['vote'];
            }
        }
    }

    public static function amIVoted($id,$type = "my") {

        $uid = Yii::$app->user->id;

        return Yii::$app->db->createCommand("

        Select count(id)
		from {{%rating_votes}} v
		where rid = (
                        SELECT rid
						FROM {{%rating_total}}
						WHERE nid = $id
						LIMIT 1
                )
        and uid = $uid
        and name = '$type'
        "
        )->queryScalar();
    }

    public function save() {



        /*
         * Razz only
         */

        $created = Yii::$app->db->createCommand('SELECT created_at FROM {{%razz}} razz WHERE id=:nid ', [':nid' => $this->nid])->queryScalar();
        if ((\frontend\models\Razz::DAYS + $created) < time())
            return;

        if (!$this->isRated())
            return;

        /*
         *
         */

        $rid = Yii::$app->db->createCommand('SELECT id FROM {{%raiting}} raiting WHERE nid=:nid AND model=:model LIMIT 1', [':nid' => $this->nid, ':model' => $this->model])->queryScalar();

        if (!$rid) {

            Yii::$app->db->createCommand()->insert('{{%raiting}}', [
                'nid' => $this->nid,
                'model' => $this->model,
            ])->execute();
            $rid = Yii::$app->db->getLastInsertID();
        }




        foreach ($this->vote as $name => $vote) {

            if ($this->uid) {

                $id = Yii::$app->db->createCommand('SELECT id FROM {{%rating_votes}} rating_votes WHERE rid=:rid AND uid=:uid AND name=:name LIMIT 1', [
                            ':rid' => $rid,
                            ':uid' => $this->uid,
                            ':name' => $name
                        ])->queryScalar();
            } else {

                $id = Yii::$app->db->createCommand('SELECT id FROM {{%rating_votes}} rating_votes WHERE rid=:rid AND name=:name AND ip=:ip  LIMIT 1', [
                            ':rid' => $rid,
                            ':name' => $name,
                            ':ip' => $_SERVER['REMOTE_ADDR'],
                        ])->queryScalar();
            }

            if ($id) {

                Yii::$app->db->createCommand()->update('{{%rating_votes}}', [
                    'vote' => $vote ? $vote : 0,
                        ], 'id = ' . $id)->execute();
            } else {

                Yii::$app->db->createCommand()->insert('{{%rating_votes}}', [
                    'rid' => $rid,
                    'name' => $name,
                    'uid' => $this->uid,
                    'vote' => $vote,
                    'ip' => $_SERVER['REMOTE_ADDR'],
                ])->execute();
            }

            // обновление глоального  рейтинга
            $data = (new \yii\db\Query())
                            ->select('COUNT(id) c,SUM(vote) v, name,rid')
                            ->from('{{%rating_votes}}')
                            ->indexBy('name')
                            ->where([
                                'rid' => $rid,
                                'name' => $name,
                            ])->all(); //->createCommand()

            $this->return['my'] = 0;
            $this->return['responder'] = 0;


            foreach (['my', 'responder'] as $itm) {

                $id = Yii::$app->db->createCommand('SELECT id,votes FROM {{%rating_total}} WHERE rid = :rid AND name=:name LIMIT 1', [
                            'rid' => $rid,
                            'name' => $itm,
                        ])->queryOne();

                if (!$id && isset($data[$itm])) {

                    Yii::$app->db->createCommand()->insert('{{%rating_total}}', [
                        'nid' => $this->nid,
                        'rid' => $rid,
                        'name' => $name,
                        'votes' => $data[$itm]['v'],
                        'rating' => $data[$itm]['v'] / $data[$itm]['c'],
                    ])->execute();
                    $this->return[$itm] = $data[$itm]['v'];
                } elseif (isset($data[$itm])) {
                    Yii::$app->db->createCommand()->update('{{%rating_total}}', [
                        'votes' => $data[$itm]['v'],
                        'rating' => $data[$itm]['v'] / $data[$itm]['c'],
                            ], 'id = :id ', [
                        'id' => $id['id'],
                            // 'name' => $name,
                    ])->execute();
                    $this->return[$itm] = $data[$itm]['v'];
                }

                if (!isset($data[$itm]) && $id)
                    $this->return[$itm] = $id['votes'];
            }

            return $this->return;
        }
    }

}
