<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Alias;

/**
 * AliasSearch represents the model behind the search form about `backend\models\Alias`.
 */
class AliasSearch extends Alias
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'eid'], 'integer'],
            [['model', 'url', 'alias'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
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
    public function search($params)
    {
        $query = Alias::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'eid' => $this->eid,
        ]);

        $query->andFilterWhere(['like', 'model', $this->model])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'alias', $this->alias]);

        return $dataProvider;
    }
}
