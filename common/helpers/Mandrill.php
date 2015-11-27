<?php

    namespace common\helpers;

    use Mandrill as Mandr;
    use Mandrill_Error;
    use Yii;
    use yii\base\Exception;
    use yii\base\Object;

    /**
     * Class Mandrill
     *
     * @package common\helpers
     *
     * <code>
     *    $mailer = new \common\helpers\Mandrill(
     *      'to_user@yandex.com',
     *      'Welcome to Razzd!',
     *          $local_tpl_name = null,
     *          $sender = null,
     *           [
     *              'from_name' => 'Admin name',
     *              'reply_to' => 'admin@razzd.com',
     *              'mandrill_template_name' => 'remote template name from mandrillapp.com',
     *              'vars' => [
     *                  'subj' => 'HI',
     *                  'body' => 'This is a body of message',
     *              ]
     *           ]
     *      );
     *
     * $result =  $mailer->sendWithMandrillTemplate();
     * </code>
     */
    class Mandrill extends Object
    {
        /**
         * Email
         * @var null|string
         */
        protected $to;

        /**
         * Email subject
         * @var null|string
         */
        protected $subject;

        /**
         * @var string
         */
        protected $sender;

        /**
         * @var string
         */
        protected $replyTo;

        /**
         * Template name
         * @var null|string
         */
        protected $view;

        /**
         * Message content (html|text)
         * @var null
         */
        protected $body;

        /**
         * Message header
         * @var null
         */
        protected $header;

        /**
         * Extra params
         * @var array
         */
        protected $params;

        /**
         * @var
         */
        protected $apikey;

        /**
         * template name on mandrillapp.com
         * @var
         */
        protected $mandrill_template_name;

        /**
         *
         * @var null
         */
        protected $tags;

        /**
         * @var null
         */
        protected $fromName;

        /**
         * Template variables
         * @var array {key:val, ke2:val2}
         */
        protected $vars;

        /**
         * User object
         * @var
         */
        protected $user;

        /**
         * @var string
         */
        protected $viewPath = '@common/mail';

        /**
         * @param string|null $to
         * @param string|null $subject
         * @param string|null $view
         * @param string|null $sender
         * @param array $params{'user', 'reply_to', 'body', 'header', 'mandrill_template_name', 'tags', 'vars', 'from_name'}
         */
        function __construct($to = null, $subject = null, $view = null, $sender = null, $params = [])
        {
            $this->to = $to;
            $this->subject = $subject;
            $this->view = $view;
            $this->params = $params;

            $c1 = $c2 = $c3 = $c4 = $cfg = [];

            if (file_exists(\Yii::getAlias('@app') . '/../common/config/' . '/main-local.php')) {
                $c1 = require(\Yii::getAlias('@app') . '/../common/config/' . '/main-local.php');
            }
            if (file_exists(\Yii::getAlias('@app') . '/../common/config/' . '/main.php')) {
                $c2 = require(\Yii::getAlias('@app') . '/../common/config/' . '/main.php');
            }
            if (file_exists(\Yii::getAlias('@app') . '/../frontend/config/' . '/main.php')) {
                $c3 = require(\Yii::getAlias('@app') . '/../frontend/config/' . '/main.php');
            }
            if (file_exists(\Yii::getAlias('@app') . '/../frontend/config/' . '/main-local.php')) {
                $c4 = require(\Yii::getAlias('@app') . '/../frontend/config/' . '/main-local.php');
            }

            $cfg = \Yii\helpers\BaseArrayHelper::merge($c1, $c2);
            $cfg = \Yii\helpers\BaseArrayHelper::merge($cfg, $c3);
            $cfg = \Yii\helpers\BaseArrayHelper::merge($cfg, $c4);

            if (isset($cfg['components']['mailer'])) {
                $mailer_config = $cfg['components']['mailer'];
            }

            if (isset($cfg['components']['mailer']['apikey'])) {
                $this->apikey = $cfg['components']['mailer']['apikey'];
            }

            if (isset($mailer_config['viewPath'])) {
                $this->viewPath = $mailer_config['viewPath'];
            }

            if ($this->sender === null) {
                $this->sender = isset(\Yii::$app->params['adminEmail']) ? \Yii::$app->params['adminEmail'] : 'no-reply@example.com';
            }

            if ($this->replyTo === null) {
                $this->replyTo = isset($this->params['reply_to']) ? $this->params['reply_to'] : 'info@razzd.com';
            }

            if ($this->body === null) {
                $this->body = isset($this->params['body']) ? $this->params['body'] : null;
            }

            if ($this->header === null) {
                $this->header = isset($this->params['header']) ? $this->params['header'] : null;
            }

            if ($this->mandrill_template_name === null) {
                $this->mandrill_template_name = isset($this->params['mandrill_template_name']) ? $this->params['mandrill_template_name'] : null;
            }

            if ($this->tags === null) {
                $this->tags = isset($this->params['tags']) ? $this->params['tags'] : ['razzd'];
            }

            if ($this->vars === null) {
                $this->vars = isset($this->params['vars']) ? $this->params['vars'] : null;
            }

            if ($this->fromName === null) {
                $this->fromName = isset($this->params['from_name']) ? $this->params['from_name'] : 'Your friendly manager';
            }

        }

        /**
         * Simply send using local php template
         *
         * @param string|null $to
         * @param string|null $subject
         * @param string|null $sender
         * @param string|null $view
         * @param array $params
         * @return bool
         *
         * <code>
         *  $mailer->sendMessage("to_user@domen.com","Hi, Username!","replay_to_admin@domen.com","PHPTemplate_name",['key' => $val]);
         * </code>
         */
        function sendMessage($to = null, $subject = null, $sender = null, $view = null, $params = [])
        {
            if (is_null($to)) {
                $to = $this->to;
            }
            if (is_null($subject)) {
                $subject = $this->subject;
            }
            if (is_null($sender))
                $sender = $this->sender;
            if (is_null($view)) {
                $view = $this->view;
            }
            if (!sizeof($params)) {
                $params = $this->params;
            }

            $mailer = \Yii::$app->mailer;
            $mailer->viewPath = $this->viewPath;
            $mailer->getView()->theme = \Yii::$app->view->theme;

            // if(is_null($sender))  $sender = isset(\Yii::$app->params['adminEmail']) ? \Yii::$app->params['adminEmail'] : 'no-reply@example.com';

            return $mailer->compose(['html' => $view, 'text' => 'text/' . $view], $params)
                ->setTo($to)
                ->setFrom($sender)
                ->setSubject($subject)
                ->send();
        }


        /**
         * @return bool
         * @throws Mandrill_Error
         */
        function sendWithMandrillTemplate()
        {
            $sendTo = $this->to;
            $replyTo = $this->replyTo;

            // $body = $this->body;
            // $usermail = $this->to;
            // $message_subject = $this->header;

            $subject = $this->subject;
            $template_name = $this->mandrill_template_name;

            $result = null;

            $template_content = array(
                array(
                    // fixme (its doesnot work)
                    // Put 'example content' to block named 'example name'
                    'name' => 'subjjject',
                    'content' => 'example subj content-'
                )
            );

            $message = array(
                'html' => '<p>Example HTML content</p>',
                'text' => 'Example text content',
                'subject' => $subject,
                'from_email' => $this->replyTo,
                'from_name' => $this->fromName,
                'to' => array(
                    array(
                        'email' => $sendTo,
                        'name' => 'Recipient Name',
                        'type' => 'to'
                    )
                ),
                'headers' => array('Reply-To' => $replyTo),
                'important' => false,
                'track_opens' => null,
                'track_clicks' => null,
                'auto_text' => null,
                'auto_html' => null,
                'inline_css' => null,
                'url_strip_qs' => null,
                'preserve_recipients' => null,
                'view_content_link' => null,
                'bcc_address' => '',
                'tracking_domain' => null,
                'signing_domain' => null,
                'return_path_domain' => null,
                'merge' => true,
                'merge_language' => 'mailchimp',
                'global_merge_vars' => array(
                    array(
                        'name' => 'subject_internal',
                        'content' => 'global message subject'
                    ),
                    array(
                        'name' => 'body',
                        'content' => 'global message body'
                    ),
                ),
                'merge_vars' => array(
                    array(
                        'rcpt' => $sendTo,
                        'vars' => $this->prepareVars()
                    )
                ),
                'tags' => array($this->tags),
                'metadata' => array('website' => 'www.razzd.com'),
                // 'recipient_metadata' => array(
                //    array(
                //        'rcpt' => 'recipient.email@example.com',
                //        'values' => array('user_id' => 123456)
                //    )
                // ),
                // 'attachments' => array(
                //     array(
                //         'type' => 'text/plain',
                //         'name' => 'myfile.txt',
                //         'content' => 'ZXhhbXBsZSBmaWxl'
                //     )
                // ),
                // 'images' => array(
                //    array(
                //        'type' => 'image/png',
                //        'name' => 'IMAGECID',
                //        'content' => 'ZXhhbXBsZSBmaWxl'
                //    )
                // )
            );

            try {

                $mandrill = new Mandr($this->apikey);

                $result = $mandrill->messages->sendTemplate($template_name, $template_content, $message);

            } catch(Mandrill_Error $e) {

                //echo 'A mandrill error occurred: ' . get_class($e) . ' - ' . $e->getMessage();

                //throw $e;

                // TODO
            } catch(Exception $e){

            }

            if( isset($result[0]['status']) )
                return $result[0]['status'];

            return true;
        }

        /**
         * @return array
         * @throws Exception
         */
        private function prepareVars(){

            if(is_null($this->vars))
                throw new Exception("Vars are not set");

            if(!is_array($this->vars))
                throw new Exception("Vars is not array");

            $vars = [];

            foreach($this->vars as $key => $value){
                $vars[] = ['name' => $key, 'content' => $value];
            }

            return $vars;
        }
    }