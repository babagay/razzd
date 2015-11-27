<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Razz;

/**
 * RazzSearch represents the model behind the search form about `backend\models\Razz`.
 */
class RazzSearch extends Razz {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'uid', 'type', 'ended', 'responder_uid', 'views', 'views_at', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'description', 'message', 'stream', 'stream_preview', 'responder_stream', 'responder_stream_preview', 'email', 'hash'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params) {
        $query = Razz::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (is_numeric($this->uid))
            $query->andWhere([
                'uid' => $this->uid ? $this->uid : null,
            ]);

        if (is_numeric($this->responder_uid))
            $query->andWhere([
                'responder_uid' => $this->responder_uid ? $this->responder_uid : null,
            ]);

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'ended' => $this->ended,
            'views' => $this->views,
            'views_at' => $this->views_at,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
                ->andFilterWhere(['like', 'description', $this->description])
                ->andFilterWhere(['like', 'message', $this->message])
                ->andFilterWhere(['like', 'stream', $this->stream])
                ->andFilterWhere(['like', 'stream_preview', $this->stream_preview])
                ->andFilterWhere(['like', 'responder_stream', $this->responder_stream])
                ->andFilterWhere(['like', 'responder_stream_preview', $this->responder_stream_preview])
                ->andFilterWhere(['like', 'email', $this->email])
                ->andFilterWhere(['like', 'hash', $this->hash]);

        return $dataProvider;
    }

}
