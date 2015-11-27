<?php

namespace frontend\widgets\rating;

use yii\web\AssetBundle;

/**
 * Widget asset bundle
 */
class Asset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $basePath = '@webroot';
    //public $baseUrl = '@web/frontend/widgets/rating';
    public $sourcePath = '@app/widgets/rating/assets';
    //public $sourcePath = 'assets';
    /**
     * @var string Redactor language
     */
    public $language;

    /**
     * @var array Redactor plugins array
     */
    public $plugins = [];

    /**
     * @inheritdoc
     */
    public $css = [
        'rating.css'
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'jquery.barrating.min.js',
        'rating.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
    ];

}
