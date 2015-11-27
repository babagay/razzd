<?php

namespace frontend\models;

class Account extends \dektrium\user\models\Account {

    public function afterSave($insert, $changedAttributes) {
        if (!$insert) {
            $accounts = [];
            $accounts[$this->provider] = $this;
            User::assignRazz(new User(), $accounts);
        }
        parent::afterSave($insert, $changedAttributes);
    }

}
