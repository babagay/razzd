<?php

    namespace frontend\assets;

    use yii\web\AssetBundle;

    class OrangeAsset extends AssetBundle {

        public $basePath = '@webroot';
        public $baseUrl = '@web';
        public $css = [
            'https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css',
            'http://cdn.jsdelivr.net/jquery.slick/1.5.7/slick.css',
            'css/site.css',
        ];
        public $js = [
            'http://cdn.jsdelivr.net/jquery.slick/1.5.7/slick.min.js',
            'js/scripts.js',
        ];
        public $depends = [
            'yii\web\YiiAsset',
            'yii\bootstrap\BootstrapAsset',
        ];

    }