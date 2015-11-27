<?php
    /**
     * Custom email template
     */
?>

<p>Congratulations <?= $user->username ?>! <br>You are now registered for Razzd.  We appreciate you joining us and look forward to providing you with the most interactive and useful application available.</p>

<p>Here is what you can do:</p>

<ul>
    <li>RAZZ SOMEONE - Challenge someone regarding any issue or topic.  State your opinion and challenge them to respond.  Once the challenge is accepted your challenge goes live for the public to vote on who’s right and who’ wrong.  After 3 days of voting, whoever has the most votes wins (of course that will be you)!</li>
    <li>RAZZ ANYONE - State your opinion about any issue or headline and let anyone in the public oppose your view.  Once a challenge is accepted it goes live for the public to vote on who’s right and who’s wrong.  After 3 days of voting, whoever has the most votes wins (of course that will be you)!</li>
    <li>Browse through all the Razz Anyone challenges and respond with your opposing view.  Once you respond the challenge goes live for the public to vote on who’s right and who’s wrong.  After 3 days of voting, whoever has the most votes wins (of course that will be you)!</li>
    <li>Browse through all the live challenges and vote on who you think is right.</li>
</ul>

<p>Don’t forget to view your “My Account” which tracks your challenges and your votes to show your winning percentage.  Are you a novice or a guru?  The more you win the higher your status.  Remember no one messes with a Razzd Guru.</p>

<p>We look forward to making this experience unique, fun, informative, and the best way to express yourself, your opinions, and prove you are right!!!</p>

<p>Invite your friends to join <?= Yii::$app->name ?>.</p>

<p>Thank you, <?= $user->username ?>! </p>

<p>Your friendly online dispute manager.</p>