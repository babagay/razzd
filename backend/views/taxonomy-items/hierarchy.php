<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\widgets\ActiveForm;
use yii\web\JqueryAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TaxonomyItemsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Hierarchy';
$this->params['breadcrumbs'][] = ['label' => 'Taxonomy Vocabularies','url' => '/admin/taxonomy-vocabulary'];
$this->params['breadcrumbs'][] = 'Ordering'; 


?>
<div class="taxonomy-items-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
<?php if($model->vid):?>
    
    <?php $form = ActiveForm::begin(); ?>

    <div class="form-group">
        <?= Html::submitButton( 'Save', ['class' => 'btn btn-primary']) ?> 
    </div>
    

    <?php ActiveForm::end(); ?>
   
    <div class="dd">
        
        <?= Yii::$app->controller->ol($tree);?>
          
    </div>
    
 
    
    <?php 
        $this->registerJsFile('/admin/js/jquery.nestable.js', ['depends' => [JqueryAsset::className()]]);
        $this->registerJs(" 
            
        $('.dd').nestable({maxDepth:10,group:1 }); 
        $('.dd').on('change', function() {
            $('form#w0 button').removeAttr('disabled'); 
        });
        
        $('form#w0 button').attr('disabled', 'disabled');
        $('form#w0 button').click(function(){ 
            
            var data = $('.dd').nestable('serialize');
           
            $.post('', {vid:".$model->vid.",data:data}, function(){  $('form#w0 button').attr('disabled', 'disabled'); return false; });

            return false; 
        });");
    ?>
  <?php endif; ?>
</div>
