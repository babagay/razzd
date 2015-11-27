<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

$this->title = 'Profile';
/*$this->params['breadcrumbs'][] = $this->title;*/
?>
<div class="banner-convert">
<div class="vertical-banner">
	<!--img  src="/images/vertical-banner.gif" alt="image_description"-->
</div>
<div class="site-profile add-banner-off">

    <div class="jumbotron">
		<section class="profile-section">
			<div class="profile-info-section">
				<img class="blue-border" src="/images/profile-img.png" alt="image_description">
				<div class="profile-info">
					<h2>JOHN DOE</h2>
					<span>LEVEL: <a href="#"> NOVICE</a></span>
					<span>RAZZ'S COMPLETED: <a href="#"> 1</a></span>
					<span>TOTAL VIEWS: <a href="#"> 50</a></span>
					<span>TOTAL VOTES: <a href="#"> 20</a></span>
					<span>CHALLENGES WON: <a href="#"> 1</a></span>
				</div><!--  /profil-info  --> 
			</div><!--  /profil-info-section  --> 
			<div class="notifications">
				<h2>NOTIFICATIONS</h2>
				<div class="notifications-list">
					<div class="notifications-li">
						<span class="text">YOU HAVE BEEN RAZZD BY JANE DOE</span>
						<span class="close">X</span>
						<a href="#" class="btn">RESPOND</a>
					</div>
					<div class="notifications-li">
						<span  class="text">YOU WON <a href="#">THE PRACTICAL CORVETTE</a></span>
						<span class="close">X</span>
						<a href="#" class="btn">VIEW</a>
					</div>
				</div>
			</div><!--  /profil-info-section  --> 
		</section><!--  /profil-section  --> 
		<section class="pending-razzs   border-bottom">
			<h2>PENDING RAZZS</h2>
			<div class="video-list video-list-small all-list">
        				<div class="video-li">
        					<div class="visual-sections">
	        					<div class="visual-section">
	        						<div class="visual-person">
	        							<img class="blue-border" src="/images/video-img03.jpg" alt="image_description">	
	        						</div>
	        						<div class="info-person">
	        							<a href="#" class="btn  btn-big">PENDING</a>
	        							<span>
	        								THE PRACTICAL CORVETTE<br>
											CATEGORY: <a href="#">LOVE/RELATIONSHIPS</a><br>
											RAZZEE: <a href="#">NAME@NAME.COM</a>
										</span>
	        						</div><!-- info-person  -->
	        					</div>	<!-- visual-section  -->
	        				</div><!-- visual-sections  -->
        				</div><!-- /video-li  -->
        				
        				<div class="video-li">
        					<div class="visual-sections">
	        					<div class="visual-section">
	        						<div class="visual-person">
	        							<img class="blue-border" src="/images/video-img03.jpg" alt="image_description">	
	        						</div>
	        						<div class="info-person">
	        							<a href="#" class="btn btn-big">PENDING</a>
	        							<span>
	        								THE PRACTICAL CORVETTE<br>
											CATEGORY: <a href="#">LOVE/RELATIONSHIPS</a><br>
											RAZZEE: <a href="#">NAME@NAME.COM</a>
										</span>
	        						</div><!-- info-person  -->
	        					</div>	<!-- visual-section  -->
	        				</div><!-- visual-sections  -->
        				</div><!-- /video-li  -->
        				
        				<div class="video-li">
        					<div class="visual-sections">
	        					<div class="visual-section">
	        						<div class="visual-person">
	        							<img class="blue-border" src="/images/video-img03.jpg" alt="image_description">	
	        						</div>
	        						<div class="info-person">
	        							<a href="#" class="btn btn-big">PENDING</a>
	        							<span>
	        								THE PRACTICAL CORVETTE<br>
											CATEGORY: <a href="#">LOVE/RELATIONSHIPS</a><br>
											RAZZEE: <a href="#">NAME@NAME.COM</a>
										</span>
	        						</div><!-- info-person  -->
	        					</div>	<!-- visual-section  -->
	        				</div><!-- visual-sections  -->
        				</div><!-- /video-li  -->
        				
        				<div class="video-li">
        					<div class="visual-sections">
	        					<div class="visual-section">
	        						<div class="visual-person">
	        							<img class="blue-border" src="/images/video-img03.jpg" alt="image_description">	
	        						</div>
	        						<div class="info-person">
	        							<a href="#" class="btn btn-big">PENDING</a>
	        							<span>
	        								THE PRACTICAL CORVETTE<br>
											CATEGORY: <a href="#">LOVE/RELATIONSHIPS</a><br>
											RAZZEE: <a href="#">NAME@NAME.COM</a>
										</span>
	        						</div><!-- info-person  -->
	        					</div>	<!-- visual-section  -->
	        				</div><!-- visual-sections  -->
        				</div><!-- /video-li  -->
        				

        			</div><!-- /video-list  -->
		</section>
		
		<section class="my-razzs">
        			<h2 >
        				vote-on-challenges
        			</h2>
        			<div class="video-list all-list">
        				<div class="video-li">
        					<div class="visual-sections">
	        					<div class="visual-section">
	        						<div class="visual-person">
	        							<img class="blue-border" src="/images/video-img02.jpg" alt="image_description">	
	        						</div>
	        						<div class="info-person">
	        							<span class="title"><a href="#">queenb (3)</a></span>
	        							<ul class="vote-info">
	        								<li><a href="#" class="btn btn-small">LOSING</a></li>
	        							</ul>
	        						</div><!-- info-person  -->
	        					</div>	<!-- visual-section  -->
	        					<div class="visual-section">
	        						<div class="visual-person">
	        							<img class="blue-border" src="/images/video-img01.jpg" alt="image_description">	
	        						</div>
	        						<div class="info-person">
	        							<span class="title"><a href="#">turnbough (3)</a></span>
	        							<ul class="vote-info">
	        								<li><a href="#" class="btn btn-small">WINNING!</a></li>
	        							</ul>
	        						</div><!-- info-person  -->
	        					</div>	<!-- visual-section  -->
	        				</div><!-- visual-sections  -->
	        				<div class="description-video">
	        					<a href="#" class="btn">WATCH</a>
	        					<span>LIVING ROOM </span><br>
	        					<span>CATEGORY: <a href="#">LOVE/RELATIONSHIPS</a>  </span><span>  VIEWS: <a href="#">706</a> </span><br>
	        					<span>TIME REMAINING: <a href="#"> 10 DAYS 02 HOURS 20 MINUTES</a></span>
	        				</div><!-- /description-video  -->
        				</div><!-- /video-li  -->
        				
        				<div class="video-li">
        					<div class="visual-sections">
	        					<div class="visual-section">
	        						<div class="visual-person">
	        							<img class="blue-border" src="/images/video-img02.jpg" alt="image_description">	
	        						</div>
	        						<div class="info-person">
	        							<span class="title"><a href="#">queenb (3)</a></span>
	        							<ul class="vote-info">
	        								<li><a href="#" class="btn btn-small">LOSING</a></li>
	        							</ul>
	        						</div><!-- info-person  -->
	        					</div>	<!-- visual-section  -->
	        					<div class="visual-section">
	        						<div class="visual-person">
	        							<img class="blue-border" src="/images/video-img01.jpg" alt="image_description">	
	        						</div>
	        						<div class="info-person">
	        							<span class="title"><a href="#">turnbough (3)</a></span>
	        							<ul class="vote-info">
	        								<li><a href="#" class="btn btn-small">WINNING!</a></li>
	        							</ul>
	        						</div><!-- info-person  -->
	        					</div>	<!-- visual-section  -->
	        				</div><!-- visual-sections  -->
	        				<div class="description-video">
	        					<a href="#" class="btn">WATCH</a>
	        					<span>LIVING ROOM </span><br>
	        					<span>CATEGORY: <a href="#">LOVE/RELATIONSHIPS</a>  </span><span>  VIEWS: <a href="#">706</a> </span><br>
	        					<span>TIME REMAINING: <a href="#"> 10 DAYS 02 HOURS 20 MINUTES</a></span>
	        				</div><!-- /description-video  -->
        				</div><!-- /video-li  -->
        				
        				<div class="video-li">
        					<div class="visual-sections">
	        					<div class="visual-section">
	        						<div class="visual-person">
	        							<img class="blue-border" src="/images/video-img02.jpg" alt="image_description">	
	        						</div>
	        						<div class="info-person">
	        							<span class="title"><a href="#">queenb (3)</a></span>
	        							<ul class="vote-info">
	        								<li><a href="#" class="btn btn-small">LOSING</a></li>
	        							</ul>
	        						</div><!-- info-person  -->
	        					</div>	<!-- visual-section  -->
	        					<div class="visual-section">
	        						<div class="visual-person">
	        							<img class="blue-border" src="/images/video-img01.jpg" alt="image_description">	
	        						</div>
	        						<div class="info-person">
	        							<span class="title"><a href="#">turnbough (3)</a></span>
	        							<ul class="vote-info">
	        								<li><a href="#" class="btn btn-small">WINNING!</a></li>
	        							</ul>
	        						</div><!-- info-person  -->
	        					</div>	<!-- visual-section  -->
	        				</div><!-- visual-sections  -->
	        				<div class="description-video">
	        					<a href="#" class="btn">WATCH</a>
	        					<span>LIVING ROOM </span><br>
	        					<span>CATEGORY: <a href="#">LOVE/RELATIONSHIPS </a> </span><span>  VIEWS: <a href="#">706</a> </span><br>
	        					<span>TIME REMAINING: <a href="#"> 10 DAYS 02 HOURS 20 MINUTES</a></span>
	        				</div><!-- /description-video  -->
        				</div><!-- /video-li  -->
        			</div><!-- /video-list  -->
        		</section><!-- /my-razzs  -->
    </div>

</div><!--  /site-profile  --> 
</div>