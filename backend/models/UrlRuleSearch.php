<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\UrlRule;

/**
 * UrlRuleSearch represents the model behind the search form about `app\models\UrlRule`.
 */
class UrlRuleSearch extends UrlRule {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id',], 'integer'],
            [['url', 'alias'], 'string', 'max' => 255],
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
        $query = UrlRule::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'url', $this->url])
                ->andFilterWhere(['like', 'alias', $this->alias]);

        return $dataProvider;
    }

}
