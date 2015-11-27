<?php

    use yii\helpers\Html;
    use common\helpers\DataHelper;
    use frontend\models\Razz;
    use yii\helpers\Url;
    use common\helpers\Ziggeo as ZiggeoHelper;

    /**
     * @var $razzModel frontend\models\Razz
     */

    if(!is_array($items))
        return;

    $isArchive = ( in_array('archive',explode('/',Url::to(''))) ) ? true : false;

    $winnerTitle = "WINNING :)";
    $loserTitle = "LOSING :(";

    if($isArchive){
        $winnerTitle = "Winner";
        $loserTitle = "Loser";
    }



?>

<?php foreach ($items as $itm): ?>
    <?php
    $obj = $razzModel->getRazz($itm['id']);



    ?>

    <?php if ($obj['responder_uid'] && !$obj['hash']): ?>

        <div class="video-li grid_item crsl-item item_<?= $itm['id'] ?>">
                <div class="title-visual-sections clearfix">
                    <span class="pos-left"><?= Html::encode($obj['title']) ?> </span>
                    <span  class="pos-right">VIEWS: <?= $obj['views']; ?></span>
                </div> <!--.title-visual-sections -->
                <div class="visual-sections">
                    <div class="img_wrp clearfix">
                         <div class="visual-section">
                            <div class="visual-holder">
                                <div class="visual-person <?php
                                    if(isset($itm['voted_for'])){
                                        if($itm['voted_for'] == 'my'){
                                            echo 'voted';
                                        }
                                    }
                                ?>">
                                    <?php
                                        if(isset($itm['voted_for'])){
                                            if($itm['voted_for'] == 'my'){
                                                echo '<span class="voted_text">voted</span>';
                                            }
                                        }
                                    ?>

                                    <?= ZiggeoHelper::getImage($obj['id'], $obj['stream'],$obj['stream_preview'], false, "") ?>
                                    <ul class="vote-info">
                                        <li>
                                                <?php
                                                    if ($obj['my_votes'] > $obj['responder_votes'])
                                                        echo $winnerTitle;
                                                    if ($obj['my_votes'] < $obj['responder_votes'])
                                                        echo $loserTitle;
                                                    if ($obj['my_votes'] == $obj['responder_votes'])
                                                        if($obj['my_votes'] != 0)
                                                           echo 'Draw';
                                                ?>
                                             </li>
                                    </ul>
                                </div>
                            </div>
                        
                    </div>  <!-- visual-section  -->
                    <div class="visual-section">
                        <div class="visual-holder">
                            <?php
                                $voted_1 = "";
                                if(isset($itm['voted_for']))
                                    if($itm['voted_for'] == 'responder')
                                         $voted_1 = 'voted';

                                $voted_box_1 = "";
                                if(isset($itm['voted_for']))
                                    if($itm['voted_for'] == 'responder')
                                        $voted_box_1 = '<span class="voted_text">voted</span>';

                            ?>
                            <div class="visual-person <?= $voted_1 ?>">
                                <?= $voted_box_1 ?>
                                <?= ZiggeoHelper::getImage($obj['id'], $obj['responder_stream'],$obj['responder_stream_preview'],true,"") ?>
                                 <ul class="vote-info">
                                    <li>
                                            <?php
                                                if ($obj['my_votes'] < $obj['responder_votes'])
                                                    echo $winnerTitle;
                                                if ($obj['my_votes'] > $obj['responder_votes'])
                                                    echo $loserTitle;
                                                if ($obj['my_votes'] == $obj['responder_votes'])
                                                    if($obj['my_votes'] != 0)
                                                        echo 'Draw';
                                            ?>
                                        </li>
                                </ul>
                            </div>
                        </div>

                    </div>  <!-- visual-section  -->
                    </div>
                    <div class="info_wrp">
                        <div class="info-person">
                            <span class="title"><?= Html::a($obj['name1'] . ' (' . $obj['my_votes'] . ')', ['/user/profile/show', 'id' => $obj['uid']]); ?></span>
                            
                        </div><!-- info-person  -->
                        <div class="info-person">
                            <span class="title"><?= Html::a($obj['name2'] . ' (' . $obj['responder_votes'] . ')', ['/user/profile/show', 'id' => $obj['responder_uid']]); ?></span>
                            
                        </div><!-- info-person  -->
                    </div>
                </div><!-- visual-sections  -->
                <div class="description-video text-center">
                    <a href="/razz/<?= $obj['id'] ?>" class="icon icon-right icon-big icon-watch">
                        <?php
                            $Link_name = "Watch &amp; Vote";

                            if(isset($itm['voted_for'])){
                                $Link_name =  "Voted";
                            } else {
                                if(isset($model))
                                    if(isset($model->isArchive))
                                        if($model->isArchive === true){
                                            $Link_name = "Watch";
                                        }
                            }

                            echo $Link_name;
                        ?>
                    </a>
                </div><!-- /description-video  -->
        </div><!-- /video-li  -->
    <?php else: ?>

        <div class="video-li small crsl-item item_<?= $itm['id'] ?>">
            <div class="visual-sections">
                <div class="visual-section">
                    <div class="title-visual-sections razee-title clearfix">
                        <span class="pos-left"><?= Html::encode($obj['title']) ?></span>
                        <span  class="pos-right">By: <?= Html::a($obj['name1'], ['/user/profile/show', 'id' => $obj['uid']]); ?></span>
                    </div> <!--.title-visual-sections -->
                    <div class="visual-person">
                        <?= ZiggeoHelper::getImage($obj['id'], $obj['stream'],$obj['stream_preview'], false, "") ?>
                    </div>
                    <div class="info-person text-center">
                    <?php
                        $respondLink = "<a href=\"/razz/respond/". $obj['id'] ."\" class=\"icon icon-right icon-big icon-info-person\">RESPOND</a>";

                        if(isset($nonLinkable))
                            if($nonLinkable === true)
                                $respondLink = "";

                            if (!$obj['responder_uid'] || (isset($profile) && ($obj['responder_uid'] == $profile->id)))
                                echo $respondLink;
                    ?>
                    </div><!-- info-person  -->
                </div>	<!-- visual-section  -->
            </div><!-- visual-sections  -->
        </div><!-- /video-li  -->


    <?php endif; ?>


<?php endforeach; ?>

