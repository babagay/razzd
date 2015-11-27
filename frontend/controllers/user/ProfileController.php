<?php

/*
 * This file is part of the Dektrium project.
 *
 * (c) Dektrium project <http://github.com/dektrium/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace frontend\controllers\user;

use Yii;
use dektrium\user\controllers\ProfileController as BaseProfileController;
use frontend\models\UserStat;
use yii\web\NotFoundHttpException;
use frontend\models\Razz;
use frontend\models\RazzSearch;
use frontend\models\Notification;
use yii\filters\AccessControl;
use frontend\models\ProfileImage;
use yii\web\UploadedFile;

/**
 * ProfileController shows users profiles.
 *
 * @property \dektrium\user\Module $module
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ProfileController extends BaseProfileController {

    public function behaviors() {
	return [
	    'access' => [
		'class' => AccessControl::className(),
		'rules' => [
		    ['allow' => true, 'actions' => ['index'], 'roles' => ['@']],
		    ['allow' => true, 'actions' => ['show'], 'roles' => ['?', '@']],
		    ['allow' => true, 'actions' => [ 'upload'], 'roles' => ['@']],
		]
	    ],
	];
    }

    /**
     * Shows user's profile.
     * @param  integer $id
     * @return \yii\web\Response
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionShow($id) {

	$profile = $this->finder->findProfileById($id);
	$stat = new UserStat();
	$notification = new Notification();
	$stat->user = $profile ? $profile->user : $this->finder->findUserById($id);
	$razzModel = new Razz();
	$razzSearch = new RazzSearch();
	$image = ProfileImage::getUserImage($stat->user->id);
	$imageModel = new ProfileImage();
	if ($stat->user === null) {
	    throw new NotFoundHttpException;
	}

	return $this->render('show', [
		    'profile' => $stat->user,
		    'stat' => $stat,
		    'razzModel' => $razzModel,
		    'razzSearch' => $razzSearch,
		    'notification' => $notification,
		    'image' => $image,
		    'imageModel' => $imageModel,
	]);
    }

    public static function getRazzdUserVoted() {

	if (Yii::$app->user->isGuest)
	    return null;

	$razz = new Razz();

	return $razz->getRazzdByUserVoted(Yii::$app->user->id);
    }

    public function actionUpload() {
	if (Yii::$app->request->isPost && !Yii::$app->user->isGuest) {

	    $model = new ProfileImage();

	    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    $model->fullPath = UploadedFile::getInstance($model, 'fullPath');
	    try {
		if ($model->fullPath && $model->validate()) {
		    $dir = Yii::getAlias('@app') . DS . 'web';
		    $extension = $model->fullPath->extension;
		    $position = strrpos($model->fullPath->name, '.');
		    $fileName = substr($model->fullPath->name, 0, $position);
		    $fileHashName = md5($fileName);
		    $subDir = DS . 'files' . DS . 'profile' . DS . substr($fileHashName, 0, 1) . DS . substr($fileHashName, 1, 1);
		    $dir .= $subDir;
		    $model->file_name = $fileHashName . '_' . $fileName . '_' . time() . '.' . $extension;
		    $model->file_path = $subDir;
		    $model->user_id = Yii::$app->user->id;
		    $model->date = date('Y-m-d H:i:s');
		    if (!is_dir($dir)) {
			mkdir($dir, 0755, true);
		    }
		    $model->fullPath->saveAs($dir . DS . $model->file_name);
		    $model->fullPath = null;

		    list($width, $height) = getimagesize($dir . DS . $model->file_name);
		    $newwidth = '256';
		    $newheight = '256';
		    $wRatio = $width / $newwidth;
		    $hRatio = $height / $newheight;
		    $maxRatio = max($wRatio, $hRatio);
		    if ($maxRatio > 1) {
			$outputWidth = $width / $maxRatio;
			$outputHeight = $height / $maxRatio;
		    } else {
			$outputWidth = $width;
			$outputHeight = $height;
		    }
		    $thumb = imagecreatetruecolor($outputWidth, $outputHeight);
		    switch ($extension) {
			case 'png':
			    $source = imagecreatefrompng($dir . DS . $model->file_name);
			    break;
			case 'jpg':
			    $source = imagecreatefromjpeg($dir . DS . $model->file_name);
			    break;
			default :
			    throw new \yii\db\Exception('Wrong file extension');
		    }

		    imagecopyresized($thumb, $source, 0, 0, 0, 0, $outputWidth, $outputHeight, $width, $height);
		    imagejpeg($thumb, $dir . DS . $model->file_name);
		    $model->save(false);
		    $response = [];
		    $response['success'] = true;
		    $response['image'] = $model->file_path . DS . $model->file_name;
		    return $response;
		} else {
		    throw new \yii\db\Exception(implode('.', $model->getErrors('fullPath')));
		}
	    } catch (\Exception $ex) {
		$response['error'] = $ex->getMessage();
		return $response;
	    }
	}
    }

}
