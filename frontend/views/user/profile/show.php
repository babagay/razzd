<?php
/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium>
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

use frontend\models\RazzSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use dosamigos\fileupload\FileUpload;
use common\helpers\Html as HtmlHelper;

/**
 * @var \yii\web\View $this
 * @var \dektrium\user\models\Profile $profile
 * @var \frontend\models\UserStat $stat
 * @var array[] of razz.id $razz_ids
 */
$this->title = $profile->getFullname();
$this->registerCssFile(Url::base(true) . "/css/jquery.qtip.min.css");

$won = $stat->getChallengesWon();
$lose = $stat->getChallengesLoose();
$draw = $stat->getChallengesDraw();

$total = $won + $lose + $draw;

$percentage = $stat->getWonPercentage() == -1 ? 0 : $stat->getWonPercentage();

$total_views = is_null($stat->getTotalViews()) ? 0 : $stat->getTotalViews();
$total_votes = is_null($stat->getTotalVotes()) ? 0 : $stat->getTotalVotes();

$tip = "data-tooltip=\"
    newbie 50%-60% <br>
    novice 61%-70% <br>
    elite 71%-80% <br>
    expert 81%-90% <br>
    guru 91%-100% <br> \"
    ";

$completed = $stat->getRazzCompleted();

$status = $stat->status();

if ($status != $stat->getBasicStatus())
    $tip = "";

$notices = $notification->getNotifications($profile->id);

/**
 * На чужом профиле = true
 * Yii::$app->user->id != $profile->id
 */
$isAliensProfile = (int) Yii::$app->request->getQueryParam("id") != (int) Yii::$app->user->id;

if (!$isAliensProfile) {
    $myRazzTitle = "MY RAZZS";
} else {
    $myRazzTitle = ucfirst($profile->fullname) . "'s RAZZS";
}
?>





<div class="header <?php if (Yii::$app->user->isGuest): ?>not_registered <?php endif; ?>cf">
	<div class="user_block main_view">
		<?= HtmlHelper::logiINlogOUT("profile", $profile->id) ?>
	</div>
</div>



<div class="banner-convert">
    <div class="vertical-banner">
        <!--img  src="/images/vertical-banner.gif" alt="image_description"-->
    </div>
    <div class="site-profile add-banner-off">

        <div class="jumbotron">
            <section class="profile-section">

                <div class="profile-info-section">
                <div class="left-section">
				    <?php if ($image): ?>
		    		    <img id="profile-info-mage" src="<?php echo $image->file_path . DS . $image->file_name ?>" alt="image_description">
				    <?php else: ?>
		    		    <img id="profile-info-mage" src="/images/profile-img.png" alt="image_description">
				    <?php endif; ?>
					    <?=
					    FileUpload::widget([
						'model' => $imageModel,
						'attribute' => 'fullPath',
						'url' => ['profile/upload'],
						'options' => ['accept' => 'image/*'],
						'clientOptions' => [
						    'maxFileSize' => 5000000,
						],
						'clientEvents' => [
						    'fileuploaddone' => 'function(e, data) {
							    if(data.result.success){
								$("#profile-info-mage").attr("src",data.result.image);
							    }
							    if(data.result.error){
								$("#image-erros").html(data.result.error);
							    }
			                            }',
						    'fileuploadadd' => 'function(e, data) {  
							     if(data.originalFiles[0]["size"] != undefined && data.originalFiles[0]["size"] > 5000000) {
								$("#image-erros").html("Max file size: 5Mb");
								return false;
							    }
			                            }',
						],
					    ]);
					    ?>
                </div>
                <div class="right-section">
                	<div class="profile-info">
                        <h2><?= Html::encode($this->title); ?></h2>
                        <span>Level: <span class="highlight" <?= $tip ?>><?= $status ?></span> </span>
                        <span>Razz's completed: <span class="highlight"><?= $completed ?></span></span>
                        <span>Total views: <span class="highlight"> <?= $total_views ?></span></span>
                        <span>Total votes:  <span class="highlight"><?= $total_votes ?></span></span>
                        <span>Challenges won: <span class="highlight"><?= $won ?> </span></span>
                        <span>Winning percentage: <span class="highlight"> <?= $percentage ?>% </span></span>
                        <!--a href="/razz/archive/<?= Yii::$app->request->getQueryParam("id") ?>">Archive</a-->
                    </div><!--  /profil-info  -->
                </div>
		    <div id="image-erros">
		    </div>
                    
                </div><!--  /profil-info-section  -->

                <div class="notifications">
		    <?php
		    if ($isAliensProfile) {
			?>
    		    <span class="jcf-file razzd-btn vert_centered">
    			<a href="/razz/new/some/<?= Yii::$app->request->getQueryParam("id") ?>">Razz <?= $profile->fullname ?>!</a>
    			<!--input type="submit" value="SEND" class="btn someone-elem"-->
    		    </span>
			<?php
		    }
		    ?>
		    <?php if (sizeof($notices) AND ! $isAliensProfile) { ?>
    		    <h2>Notifications</h2>
    		    <div class="notifications-list">
			    <?php foreach ($notices as $itm): ?>
				<div class="notifications-li">
				    <span class="text"><?= $itm['message']; ?></span>
				    <?php if ($profile->id == Yii::$app->user->id): ?><noindex><a class="close" href="/site/hide-notifi-ajax?id=<?= $itm['id']; ?>" rel="nofollow">X</a></noindex>
					<?= $itm['link']; ?>
				    <?php endif; ?>
				</div>
			    <?php endforeach; ?>
    		    </div>
			<?php
		    } else {
			if (!$isAliensProfile)
			    echo "No Notifications";
		    }
		    ?>
                </div><!--  /profil-info-section  -->
            </section><!--  /profil-section  -->

	    <?php if (!$isAliensProfile): ?>

		<?php
		$pendingHtml = $this->render('../../razz/item', [
		    'items' => $razzSearch->getUserRazzRespondToChallenges($profile->id, 101),
		    'razzModel' => $razzModel,
		    'profile' => $profile
		]);

		if (trim($pendingHtml) != '') {
		    ?>

		    <section class="pending-razzs   border-bottom">
			<h2>PENDING RAZZS</h2>
			<div class="video-list video-list-small all-list">
			    <?= $pendingHtml ?>
			</div><!-- /video-list  -->
		    </section>

		<?php } ?>
	    <?php endif; ?>


	    <?php
	    $userRazzHtml = $this->render('../../razz/item', [
		'items' => $razzSearch->getUserRazzVoteOnChallenges($profile->id, 105),
		'razzModel' => $razzModel,
	    ]);

	    if (trim($userRazzHtml) != '') {
		?>
    	    <section class="my-razzs">
    		<h2>
			<?= $myRazzTitle ?>
    		</h2>
    		<div class="video-list all-list">
			<?= $userRazzHtml ?>
    		</div><!-- /video-list  -->
    	    </section><!-- /my-razzs  -->
	    <?php } ?>

	    <?php
	    if (!$isAliensProfile) {

		$votesHtml = $this->render('../../razz/item', [
		    'items' => $razzdUserVoted = frontend\controllers\user\ProfileController::getRazzdUserVoted(),
		    'razzModel' => $razzModel,
		]);

		if (trim($votesHtml) != '') {
		    ?>
		    <section class="my-razzs">
			<h2>My votes</h2>
			<div class="video-list all-list my_list">
			    <?php
			    echo $votesHtml;
			    ?>
			</div>
		    </section>
		<?php } ?>
	    <?php } ?>

	    <?php
	    $razzSearch = new RazzSearch();
	    $respondToChalHtml = $this->render('../../razz/item', [
		'items' => $razzSearch->getRazzRespondToChallenges(),
		'razzModel' => $razzModel
	    ]);

	    if (trim($respondToChalHtml) != '') {
		?>
    	    <section class="respond_items_list">
    		<h2>RESPOND TO CHALLENGES</h2>
    		<div class="video-list video-list-small all-list respond_list">
			<?= $respondToChalHtml ?>
    		</div>
    	    </section>
	    <?php } ?>

	    <?php
	    if ($isAliensProfile) {
		$user = $profile->id;
		$name = $profile->fullname . "'s ";
		$anyoneChallMess = "User doesn't have any challenges to respond to";
		$nonLinkable = false;
	    } else {
		$user = Yii::$app->user->id;
		$name = "My ";
		$anyoneChallMess = "You don't have any challenges to respond";
		$nonLinkable = true;
	    }

	    $anyoneTitle = $name . " challenges";

	    $razzSearch = new RazzSearch();
	    $anyoneHtml = $this->render('../../razz/item', [
		'items' => $razzSearch->getRazzAnyone($user),
		'razzModel' => $razzModel,
		'nonLinkable' => $nonLinkable,
	    ]);

	    if (trim($anyoneHtml) != '') {
		?>
    	    <section class="respond_items_list">
    		<h2><?= $anyoneTitle ?></h2>

    		<div class="video-list video-list-small all-list respond_list">
			<?= $anyoneHtml ?>
    		</div>
    	    </section>
		<?php
	    } else {
		echo "<h2>$anyoneChallMess</h2>";
	    }
	    ?>
        </div>

    </div><!--  /site-profile  -->
</div>


<script>
	$(function () {

	    setTimeout(function () {
		$('[data-tooltip!=""]').qtip({// Grab all elements with a non-blank data-tooltip attr.
		    content: {
			attr: 'data-tooltip' // Tell qTip2 to look inside this attr for its content
		    }
		})
	    }, 1000)

	});
</script>





