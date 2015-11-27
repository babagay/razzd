<?php

namespace frontend\models;

use Yii;
use Aws\S3\S3Client;
use yii\base\Exception;
use yii\web\UploadedFile;
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\models\Notification;

class Razz extends \yii\db\ActiveRecord {

    const SOMEONE = 1;
    const ANYONE = 2;
    const DAYS = 345600;

    public $url, $preview, $user_id;
    public $tmpDir;

    /**
     * Использовать $pureUpdate = true, когда нужно обновить одно|несколько полей записи
     * $pureUpdate = false - когда отрабатывается respond
     * @var bool
     */
    public $pureUpdate = false;

    public $category;
    public $fb_friend = null;
    public $fbFriendName = null;
    public $screen_name = null;
    public $file;
    public $tmpFileName, $fileName, $newFileName;
    public $accept = null;
    public $api = null;
    private $sendMail = null;

    public function _init() {
        $this->tmpDir = $_SERVER['DOCUMENT_ROOT'] . '/frontend/web/files/tmp';
        $this->tmpFileName = md5(Yii::$app->request->cookieValidationKey . Yii::$app->user->id);
        $this->newFileName = Yii::$app->user->id . '_' . md5(Yii::$app->request->cookieValidationKey . time() . Yii::$app->user->id);
        $this->type = $this->type ? $this->type : self::SOMEONE;
    }

    public function getInitParams(){
        $this->_init();
        return [
            'tmpDir' => $this->tmpDir,
            'tmpFileName' => $this->tmpFileName,
            'newFileName' => $this->newFileName,
            'type' => $this->type,
        ];
    }

    public function behaviors() {

        return [
            'terms' => [
                'class' => 'common\behaviors\Terms',
                'fields' => [1 => 'category:checkboxlist'], // [$vid => $fieldname]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%razz}}';
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['create'] = ['api', 'title', 'description', 'message', 'type', 'fb_friend', 'fbFriendName',  'screen_name', 'category', 'email', 'url', 'preview', 'fileName', 'file', 'accept', 'facebook_id', 'stream'];
        $scenarios['respond'] = ['api', 'fileName', 'file', 'accept'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['title', 'description', 'type', 'category'], 'required'],
            [['type', 'api', 'fb_friend'], 'integer'],
            [['category', 'fbFriendName', 'screen_name'], 'safe'],
            [['email'], 'email'],
            [['title', 'url', 'preview', 'fileName', 'facebook_id', 'stream'], 'string', 'max' => 255],
            [['description', 'message'], 'string'],
            ['file', 'file', 'extensions' => 'mp4,txt', 'maxFiles' => 1, 'maxSize' => 1024 * 1024 * 1024 * 10],
            ['url', 'required', 'when' => function ($model) {
                    return $model->api;
                }],
           ['fileName', 'required', 'when' => function ($model) {
                    return !isset($_FILES['Razz']['name']) && Yii::$app->request->isPost && !$model->api;
                }, 'whenClient' => "function (attribute, value) {
        return  $('#razz-file').val() == '';
    }"],
            /*
            ['stream', 'required', 'when' => function ($model) {
                            return  !isset($_FILES['Razz']['name']) && Yii::$app->request->isPost && !$model->api;
                        }, 'whenClient' => "function (attribute, value) {
                return  $('#razz-stream').val() == '';
            }"],
*/
            ['message', 'required', 'when' => function ($model) {
                    return $model->type == 1;
                }, 'whenClient' => "function (attribute, value) {
        return  $('#razz-type input:radio:checked').val() == 1;
    }"],
            ['accept', 'required', 'requiredValue' => 1, 'message' => 'You must accept terms', 'when' => function ($model) {
                    return $model->type == 2 && !$model->id;
                }, 'whenClient' => "function (attribute, value) {
        return  $('#razz-type input:radio:checked').val() == 2;
    }"],
            ['email', 'required', 'when' => function ($model) {
                    return $model->type == 1 && !$model->fb_friend;
                }, 'whenClient' => "function (attribute, value) {
        return  $('#razz-type input:radio:checked').val() == 1 && !$('#razz-fb_friend').val();
    }"],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'title' => 'Title',
            'description' => 'Description',
            'url' => 'Url',
            'fileName' => 'Video',
            'created_at' => 'Created At',
            'category' => 'Category',
            'updated_at' => 'Updated At',
        ];
    }

    public function afterValidate() {

        $this->_init();

        if ($this->api)
            return parent::afterValidate();

        /*
        if ($this->fileName && !is_file($this->tmpDir . '/' . $this->fileName)) {
            Yii::$app->getSession()->setFlash('error', 'There was an error uploading the file.');
            $this->addError('fileName');
        }
        */

        if (Yii::$app->request->isPost && !$this->fileName) {
            $this->file = UploadedFile::getInstance($this, 'file');
            $this->fileName = $this->tmpFileName . '.' . $this->file->extension;
            $this->file->saveAs($this->tmpDir . '/' . $this->fileName);

        }

        return parent::afterValidate();
    }

    public function beforeSave($insert) {

        $this->_init();

        $this->created_at = time();
        $this->publish = 1;
        $baseName = $baseExt = $fileToUpload = $movie = "";
        // $fileToUpload = $snapshot = $movie = "";
        $base_name = [];
        $snapshotNeeded = false;
        $result = $result2 = $this->preview = null;

      if($this->fileName != "") {
          if(preg_match('/([\w\d_]*\.mp4$)/',$this->fileName)){
              $base_name = explode(".", $this->fileName);
              $baseName = $base_name[0];
              $baseExt = $base_name[1];
              $snapshotNeeded = true;
          } else {
              $baseName = $this->fileName;
          }
      }


        if(file_exists("{$this->tmpDir}/$baseName.mp4")){
            $fileToUpload = "{$this->tmpDir}/$baseName.mp4";
            $movie = $this->newFileName . '.mp4';
        }
        /*else {
            $fileToUpload = "{$this->tmpDir}/$baseName.webm";
            $movie = $this->newFileName . '.webm';
        }
        */
              if(file_exists("{$this->tmpDir}/$baseName.png")){
                  $imgToUpload = "{$this->tmpDir}/$baseName.png";
                  $snapshot = $this->newFileName . '.png';
              } elseif(file_exists("{$this->tmpDir}/$baseName.jpg")){
                  $imgToUpload = "{$this->tmpDir}/$baseName.jpg";
                  $snapshot = $this->newFileName . '.jpg';
              } else {

                  if($snapshotNeeded) {
                      sleep(5);
                      echo shell_exec(Yii::$app->params['ffmpeg']['path'] . '/' . 'ffmpeg -ss 00:00:02 -i "' . $this->tmpDir . '/' . $baseName . '.' . $baseExt . '" -f image2 -vframes 1 "' . $this->tmpDir . '/' . $baseName . '.png"');

                  }

                  $imgToUpload = "{$this->tmpDir}/$baseName.png";
                  $snapshot = $this->newFileName . '.png';
              }

        if ($this->api) {
            // TODO
        } else {
            $this->user_id = Yii::$app->user->id;

            if($movie != ""){
                // Сохраниение видео на s3
                $s3 = new S3Client(Yii::$app->params['s3']);
                $result = $s3->putObject([
                    'Bucket' => 'razzd1',
                    'Key' => $movie,
                    'Body' => fopen($fileToUpload, 'r'),
                    'ACL' => 'public-read',
                ]);

                if (!$result) {
                    Yii::$app->getSession()->setFlash('error', 'There was an error uploading the file to cloud storage.');
                    return;
                } else {
                    $this->url = $result->search('ObjectURL');
                }

                // Сохраниение превьюшки на s3
                if(file_exists($imgToUpload)) {
                    $result2 = $s3->putObject([
                        'Bucket' => 'razzd1',
                        'Key' => $snapshot,
                        'Body' => fopen($imgToUpload, 'r'),
                        'ACL' => 'public-read',
                    ]);
                } else {

                }

                if ($result2) {
                    $this->preview = $result2->search('ObjectURL');
                }
            }

        }

        if (!$insert) {
        // Respond to existing one

            if(!$this->pureUpdate) {

                $this->responder_uid = $this->user_id;
                if ($movie != "") {
                    $this->responder_stream = $this->url;
                    $this->responder_stream_preview = $this->preview;
                } else {
                    $this->responder_stream = $this->fileName;
                }
                $this->hash = null;
            }

        } else {
        // Create new one

            $this->uid = $this->user_id;
            if($movie != "") {
                $this->stream = $this->url;
                $this->stream_preview = $this->preview;
            } else {
                // Ziggeo
                $this->stream = $this->fileName;
            }

            // SomeOne через аккаунт сайта - отправка майла
            if ($this->type == 1 && !$this->fb_friend) {
                $this->hash = md5(time() . $this->email);
                $this->sendMail = true;
                $user = \frontend\models\User::findOne(['email' => $this->email]);
                if ($user)
                    $this->responder_uid = $user->id;
            }

            /*
            if( $this->fileName != "" ){
                $this->stream_preview = \common\helpers\Ziggeo::getPreview($this->fileName);
            }
            */

            // SomeOne через социальную сеть
            if ($this->type == 1 && $this->fb_friend) {
                $this->hash = md5(time() . $this->fb_friend);
                $user_id = \frontend\models\User::getUidByClientId($this->fb_friend);

                if ($user_id)
                    // Шлём razzd внешнему пользователю, который уже есть в базе
                    $this->responder_uid = $user_id;
                else
                    // Razzd незарегистрированному внешнему пользователю
                    $this->facebook_id = $this->fb_friend;

            }
        }

        if(file_exists("{$this->tmpDir}/$baseName.png")) unlink("{$this->tmpDir}/$baseName.png");
        if(file_exists("{$this->tmpDir}/$baseName.jpg")) unlink("{$this->tmpDir}/$baseName.jpg");
        if(file_exists("{$this->tmpDir}/$baseName.wav")) unlink("{$this->tmpDir}/$baseName.wav");
        if(file_exists("{$this->tmpDir}/$baseName.mp3")) unlink("{$this->tmpDir}/$baseName.mp3");
        if(file_exists("{$this->tmpDir}/$baseName.mp4")) unlink("{$this->tmpDir}/$baseName.mp4");
        if(file_exists("{$this->tmpDir}/$baseName.webm")) unlink("{$this->tmpDir}/$baseName.webm");

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes) {

        if ($this->sendMail)
            $this->sendMailSomeOne();

        if ($this->sendMail && $this->responder_uid)
            $this->sendNotifiRazzd();

        return parent::afterSave($insert, $changedAttributes);
    }

    private static function ext($fileName) {
        return substr(strrchr($fileName, '.'), 1);
    }

    public function saveVideo() {

        $this->_init();

        //$prefix = time() . "_" . Yii::$app->user->id . "_" . str_replace(".","",$this->tmpFileName);
        $prefix = '';

        $fileName = $snapshot = '';

        if (isset($_POST["video-png"]) && isset($_POST["video-filename"])) {

            // $fileName = $_POST["video-filename"];
            //$fileName = $this->tmpFileName . '.' . self::ext($fileName);
            //$fileName = $prefix . substr($this->tmpFileName,0,10) .  self::ext($fileName);
            $fileNameArr = explode(".",$this->tmpFileName);
            $snapshot = $prefix . $fileNameArr[0] . '.png';

            // Скриншот, вариант 1
            $img = $_POST["video-png"];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);

            $file = $this->tmpDir . '/' . $snapshot;
            $success = file_put_contents($file, $data);


            // Скриншот , вариант 2
            /*
            $from_file = $this->tmpDir.'/'.$fileName;
            $to_file = $this->tmpDir.'/'."".$fileName.'.png';
            $out_file = str_replace('.webm','',$this->tmpDir.'/'.$fileName).'.mp4';
            $sound_file = str_replace('.webm','',$this->tmpDir.'/'.$fileName).'.wav';

            echo shell_exec('ffmpeg -ss 00:00:01 -i "'.$from_file.'" -f image2 -vframes 1 "'.$to_file.'"');

            // Cведение видео и аудио
            echo shell_exec("ffmpeg -i $sound_file -i $from_file $out_file");
            */

            ///sleep(3);

        }

        foreach (array('video', 'audio') as $type) {
            if (isset($_FILES["${type}-blob"])) {

                $fileName = $_POST["${type}-filename"];
                $fileName = $this->tmpFileName . '.' . self::ext($fileName);
                //$destinationFileName = $this->tmpDir . '/' . time() . "_" . $fileName;
                $destinationFileName = $this->tmpDir . '/' . $fileName;

                if (!move_uploaded_file($_FILES["${type}-blob"]["tmp_name"], $destinationFileName)) {
                    return false;
                }

                /**
                 * file - <name>.webm
                 * type - 'video' for example
                 * snapshot - <name>.webm.png
                 */
                return ['file' => $fileName, 'type' => $type, 'snapshot' => $snapshot, 'prefix' => $prefix, 'base' => str_replace('.webm','',$fileName)];
            }
        }

        return [];
    }

    public function getRazzRandom($fresh = false) {

        $query = (new \yii\db\Query())
            ->select('razz.id')
            ->from('{{%razz}} razz')
            //->innerJoin('{{%rating_total}} rating', 'rating.nid = razz.id')
            ->limit(1)
            ->where([
            // 'status' => 1,
                'publish' => 1
            ])
            ->andWhere(['not', [
                'responder_uid' => NULL,
            ]])
            ->andWhere(['is','razz.hash', NULL]);

        if($fresh){
            $query->andWhere(['>', '(razz.created_at + ' . Razz::DAYS . ')', time()]);
        }

        return $query->orderBy(['rand()' => SORT_DESC])
            ->scalar();
    }

    public function getRazz($id, $fresh = false) {

        $query = (new \yii\db\Query())
            ->select('razz.*,user.username name1,user2.username name2,t.name category,t.id tid')
            ->from('{{%razz}} razz')
            ->innerJoin('{{%user}} user', 'user.id = razz.uid')
            ->leftJoin('{{%user}} user2', 'user2.id = razz.responder_uid')
            ->leftJoin('{{%taxonomy_index}} i', 'i.nid = razz.id AND i.model="Razz"')
            ->leftJoin('{{%taxonomy_items}} t', 't.id = i.tid ')
            ->where([
                'razz.id' => $id
            ]);

        if($fresh === true){
            $query->andWhere(['>', '(razz.created_at + ' . Razz::DAYS . ')', time()]);
        }

        $razz = $query->one();

        if (!$razz)
            return;

        $razz['views'] = $this->getViewsTotalByRid($id);

        $votes = self::getRazzVotes($razz['id']);
        $razz['my_votes'] = isset($votes['my']['votes']) ? $votes['my']['votes'] : 0;
        $razz['responder_votes'] = isset($votes['responder']['votes']) ? $votes['responder']['votes'] : 0;

        return $razz;
    }

    public function  getRazzdByUserVoted($id){

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand('
        select * from (
          select r.id id,
               (
               SELECT rv.NAME
               FROM {{%rating_votes}} rv
               WHERE rv.rid = t.rid AND rv.uid = :id
               ) voted_for
          from {{%rating_total}} t, {{%razz}} r
          where t.rid IN(
        SELECT v.rid
        FROM {{%rating_votes}} v
        WHERE v.uid = :id
        GROUP BY v.rid
        )
      AND r.id = t.nid
      AND r.responder_uid IS NOT NULL
      ) razz
    GROUP BY razz.id ',
            [':id' => $id]);
        $razz = $command->queryAll();

        return $razz;
    }


    public function getRazzVotes($nid) {

        return (new \yii\db\Query())
                        ->select('votes,name')
                        ->from('{{%rating_total}} rating_total')
                        ->indexBy('name')
                        ->where([
                            'nid' => $nid,
                        ])->all();
    }

    public function toch($id) {
        Yii::$app->db->createCommand('UPDATE {{%razz}} razz SET views=views+1, views_at=UNIX_TIMESTAMP() WHERE id=:id', [':id' => $id])->execute();
    }

    public function end(&$obj) {

        if ($obj['ended'] && ($obj['created_at'] + Razz::DAYS) < time()) {
            Yii::$app->db->createCommand('UPDATE {{%razz}} razz SET ended=1 WHERE id=:id', ['id' => $obj['id']])->execute();
            $obj['ended'] = true;
        }
    }

    /**
     * Send mess to user who have started a razz
     */
    public function sendMailSomeOne() {

        $userModel = new \frontend\models\User();
        $userName = $userModel->getFullname(Yii::$app->user->id);
        $subject = $userName . ' has sent you a Razzd ‘' . $this->title . '’';
        $body = Html::encode($this->message) . '<br/><br/>' . Html::encode($this->description) . '<br/><br/>';
        $body .= '<a href="' . Url::base(true) . '/razz/respond?hash=' . $this->hash . '">Go here razzd.com</a>';

        /*
        Yii::$app->mailer->compose()
                ->setFrom([Yii::$app->params['adminEmail'] => 'Razzd'])
                ->setTo($this->email)
                ->setSubject($subject)
                ->setHtmlBody($body)
                ->send();
        */

        //--
        $userModel = new \frontend\models\User();

        //FIXME не делать проверку для юзеров, использующих аккаунт fb
//        $user = \frontend\models\User::findOne(['email' => $this->email]);
        //if(!is_object($user)) throw new Exception("No user found. No such email");
        $username = '';
        
        if ($this->type == 1 && !$this->fb_friend) {
            $user = \frontend\models\User::findOne(['email' => $this->email]);
            if ($user) {
                $username = $user->username;
            }
        } elseif ($this->type == 1 && $this->fb_friend) {
            $user = \frontend\models\User::getUserByClientId($this->fb_friend);
            if ($user) {
                $username = $user->username;
            } else {
                $username = $this->fbFriendName;
            }
        }
 
        $mailer = new \common\helpers\Mandrill(
            $sendTo = $userModel->getInfo(Yii::$app->user->id)['email'],
            $subject = "YOU HAVE SUBMITTED CHALLENGE “Razz Someone”",
            $local_tpl_name = null,
            $sender = null,
            [
                'from_name' => '[Notification generator]',
                //'reply_to' => $replyTo,
                'mandrill_template_name' => 'challenge-started-for-someone',
                'vars' => [
                    'username' => $username,
                    'startername' => $userName,
                    'title' => $this->title,
                    'link' => '<a href="' . Url::base(true) . '/razz/respond?hash=' . $this->hash . '">Link to razzd</a>'
                ]
            ]
        );

        $result =  $mailer->sendWithMandrillTemplate();

        $mess = (string)$result;

        unset($userModel);

    }

    /**
     * Send message to razzded user
     */
    private function sendNotifiRazzd() {

        $userModel = new \frontend\models\User();
        $userName = $userModel->getFullname(Yii::$app->user->id);

        $notifi = \Yii::createObject([
                    'class' => Notification::className(),
                    'uid' => $this->responder_uid,
                    'message' => 'YOU HAVE BEEN RAZZD BY ' . $userName,
                    'link' => '<a href="/razz/respond/' . $this->id . '" class="btn">RESPOND</a>',
                    'created_at' => time(),
        ]);
        $notifi->save();

        $userModel = new \frontend\models\User();
        $razdator = $userModel->getFullname(Yii::$app->user->id);

        $vis_a_vis = \frontend\models\User::findOne(['email' => $this->email]);

        $mailer = new \common\helpers\Mandrill(
            $sendTo = $this->email,
            $subject = 'YOU HAVE BEEN RAZZD BY ' . ucfirst($razdator),
            $local_tpl_name = null,
            $sender = null,
            [
                'from_name' => '[Notification generator]',
                //'reply_to' => $replyTo,
                'mandrill_template_name' => 'you-have-been-razzd',
                'vars' => [
                    'razee' => ucfirst($vis_a_vis->username),
                    'header' => $this->title,
                    'message' => $this->message,
                    'description' => $this->description,
                    'razdator' => ucfirst($razdator),
                    'link' => '<a href="'. Yii::$app->getUrlManager()->createAbsoluteUrl(["razz/respond/" . $this->id]) .'" class="btn">RESPOND</a>',
                ]
            ]
        );

        $result =  $mailer->sendWithMandrillTemplate();

        $mess = (string)$result;

        unset($userModel);
    }

    public static function getRazzUserRelated($id)
    {
        return  (new \yii\db\Query())
            ->select('razz.id')
            ->from('{{%razz}} razz')
            ->orderBy([
                'created_at' => SORT_DESC,
            ])
            ->where([
                'uid' => $id
            ])
            ->orWhere(['responder_uid' => $id])->all();
    }

    /**
     * Добавляет новый просмотр для заданного разда
     *
     * @param $rid
     * @param $uid
     * @return null|\yii\db\DataReader
     */
    public function addView($rid, $uid)
    {

        if(Yii::$app->user->isGuest)
            return false;

        $rid *= 1;
        $uid *= 1;

        if ($rid <= 0 OR $uid <= 0) {
            return null;
        }

        $razz = Yii::$app->getDb()->
        createCommand('
        SELECT *
        FROM {{%razz}}
        WHERE id = :rid',
            [':rid' => $rid])->
        queryOne();

        if (is_array($razz)) {
            if (sizeof($razz)) {

                if ((int)$razz['uid'] == $uid OR (int)$razz['responder_uid'] == $uid) {
                    return null;
                }

                // Просмотр засчитывается только для активных раздов
                if (((int)$razz['created_at'] + Razz::DAYS) < time()) {
                    return null;
                }

            } else {
                return null;
            }
        } else {
            return null;
        }

        $view = Yii::$app->getDb()->
        createCommand('
                SELECT *
                FROM {{%views}}
                WHERE uid = :uid AND rid = :rid',
            [':uid' => $uid, ':rid' => $rid])->
        queryOne();

        if (is_array($view)) {
            if (sizeof($view)) {
                return null;
            }
        }

        $command = Yii::$app->getDb()->createCommand('
                  INSERT INTO {{%views}}
                  SET uid = :uid, rid = :rid',
            [':uid' => $uid, ':rid' => $rid]);

        return $command->query();
    }

    /**
     * Количество просмотров у данного разда
     * @param $rid
     * @return array|bool
     */
    private function _getViewsTotalByRid($rid){

        $query = Yii::$app->getDb()->
        createCommand('
                        select count(id) views
                        from {{%views}} v
                        where rid = :rid',
            [':rid' => $rid]);

        return $query->
        queryOne();
    }

    function getViewsTotalByRid($rid){
        $views = $this->_getViewsTotalByRid($rid);

        if(isset($views['views']))
            return (int)$views['views'];

        return 0;
    }

    /**
     * Количество просмотров у раздов данного юзера
     * @param $uid
     * @return array|bool
     */
    function getViewsTotalByUserRazzd($uid)
    {
        $query = Yii::$app->getDb()->
        createCommand('
                        select count(t.id) views
                        from (
                            select v.id
                            from {{%views}} v
                            where rid IN (
                                                SELECT r.id
                                                FROM {{%razz}} r
                                                WHERE uid = :uid OR responder_uid = :uid
                                          )
                            ) t

        ',
            [':uid' => $uid]);

        return $query->
        queryOne();
    }


}
