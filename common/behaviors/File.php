<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\behaviors;

use yii;
use yii\base\Behavior;
use yii\base\InvalidParamException;
use yii\web\UploadedFile;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use common\helpers\TransliteratorHelper;
use yii\imagine\Image;
use Imagine\Image\ManipulatorInterface;

class File extends Behavior {

    public $fields, $count_fields = [], $recount = false, $styles = [], $id_field = 'id';
    private $model, $dir, $path, $id, $rules = [];

    public function init() {

        if (!$this->fields) {
            throw new InvalidParamException('Invalid param property: fields');
        }
    }

    private function _data() {

        $this->id = $this->owner->getAttribute($this->id_field);
        $this->model = get_class($this->owner);
        $this->model = substr($this->model, strrpos($this->model, '\\') + strlen('\\'));
        $this->path = 'files/' . strtolower($this->model);
        $this->dir = $_SERVER['DOCUMENT_ROOT'] . '/frontend/web/' . $this->path;

        foreach ($this->owner->rules() as $k => $itm) {
            if ($itm[1] == 'file') {
                $this->rules[] = $itm;
            }
        }
    }

    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_VALIDATE => 'validateFile',
            ActiveRecord::EVENT_AFTER_INSERT => 'saveFile',
            ActiveRecord::EVENT_AFTER_UPDATE => 'saveFile',
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteFiles',
            ActiveRecord::EVENT_AFTER_FIND => 'findFile'
        ];
    }

    public function deleteFiles($event) {
        $this->_data();

        $files = \common\models\File::find()->where([
                    'nid' => $this->id,
                    'model' => $this->model,
                ])->all();
        foreach ($files as $file) {

            if (is_file($_SERVER['DOCUMENT_ROOT'] . '/' . $file->path . '/' . $file->filename))
                unlink($_SERVER['DOCUMENT_ROOT'] . '/' . $file->path . '/' . $file->filename);
        }

        \common\models\File::deleteAll('nid = :nid AND model = :model', [':nid' => $this->id, ':model' => $this->model]);
    }

    public function validateFile() {

        $this->_data();
        foreach ($this->fields as $fieldName) {
            $rule = $this->getRule($fieldName);
            $files = UploadedFile::getInstances($this->owner, $fieldName);
            $maxFiles = 0;

            if (isset($rule['maxFiles']) && $rule['maxFiles'] > 1)
                $maxFiles = $rule['maxFiles'];

            if ($maxFiles) {
                $count_files_db = Yii::$app->db->createCommand('SELECT COUNT(id) FROM {{%file}} file  WHERE nid=:nid AND model=:model AND field=:field', [
                            ':nid' => $this->id,
                            ':model' => $this->model,
                            ':field' => $fieldName,
                        ])->queryScalar();

                if ((count($files) + $count_files_db) > $maxFiles) {
                    $this->owner->addError($fieldName, 'Max files: ' . $maxFiles);
                }
            }
        }
    }

    public function saveFile($event, $insert = false) {

        $this->_data();
        if (!is_dir($this->dir))
            mkdir($this->dir, 0777);

        foreach ($this->fields as $fieldName) {

            if (isset($this->styles[$fieldName])) {

                foreach ($this->styles[$fieldName] as $style) {
                    if (!is_dir($this->dir . '/' . $style))
                        mkdir($this->dir . '/' . $style, 0775);
                }
            }
        }


        foreach ($this->fields as $fieldName) {

            $rule = $this->getRule($fieldName);
            $files = UploadedFile::getInstances($this->owner, $fieldName);

            if (isset($rule['maxFiles']))
                unset($rule['maxFiles']);


            foreach ($files as $file) {

                $_model = new \common\models\File();

                $_model->rule = $rule;
                $_model->file = $file;

                if ($_model->validate()) {
                    $this->recount = true;

                    $delta = Yii::$app->db->createCommand('SELECT MAX(delta) FROM {{%file}} file  WHERE nid=:nid AND model=:model AND field=:field', [
                                ':nid' => $this->id,
                                ':model' => $this->model,
                                ':field' => $fieldName,
                            ])->queryScalar();

                    $delta = is_null($delta) ? 0 : ($delta + 1);

                    $search = [' ', ',', '\'', '`', 'К№', '(', ')'];
                    $replace = ['-', '', '', '', '', '', ''];
                    $tname = str_replace($search, $replace, TransliteratorHelper::process($_model->file->baseName));
                    $fileName = time() . '_' . $tname . '.' . $_model->file->extension;
                    $fileSize = $_model->file->size;
                    $fileType = $_model->file->type;

                    $_model->file->saveAs($this->dir . '/' . $fileName);
                    $_model->file = '';
                    $_model->nid = $this->id;
                    $_model->field = $fieldName;
                    $_model->mimetype = $fileType;
                    $_model->size = $fileSize;
                    $_model->model = $this->model;
                    $_model->filename = $fileName;
                    $_model->path = $this->path;
                    $_model->delta = $delta;
                    $_model->save();

                    /*
                     * РЎРѕР·РґР°РЅРёРµ СЃС‚РёР»РµР№ РёР·РѕР±СЂР°Р¶РµРЅРёР№
                     *
                     */
                    if (isset($this->styles[$fieldName])) {

                        foreach ($this->styles[$fieldName] as $style) {

                            $sizes = explode('x', $style);
                            $quality = 100;
                            Image::thumbnail($this->dir . '/' . $fileName, $sizes[0], $sizes[1], ManipulatorInterface::THUMBNAIL_INSET)
                                    ->save($this->dir . '/' . $style . '/' . $fileName, ['quality' => $quality]);
                        }
                    }
                } else {
                    //$errors = $_model->errors;
                    //print_r($errors);
                    //exit("A");
                }
            }
        }

        $counts = Yii::$app->db->createCommand('SELECT COUNT(id) c,field FROM {{%file}} file WHERE nid=:nid AND model=:model GROUP BY field', [
                    ':nid' => $this->id,
                    ':model' => $this->model,
                ])->queryAll();

        foreach ($counts as $count) {
            if (isset($this->count_fields[$count['field']]) && $this->owner->{$this->count_fields[$count['field']]} != $count['c']) {

                $field = $this->count_fields[$count['field']];
                $table = $this->owner->tableName();
                Yii::$app->db->createCommand('UPDATE {{%' . $table . '}} ' . $table . ' SET ' . $field . '=' . $count['c'] . ' WHERE id = ' . $this->owner->id)
                        ->execute();
            }
        }
    }

    private function getRule($field) {

        foreach ($this->rules as $rule) {
            if (in_array($field, $rule[0])) {
                $rule[0] = ['file'];
                $rule[1] = 'file';
                return $rule;
            }
        }
        return false;
    }

    public function findFile($event) {

        $this->_data();
        if (Yii::$app->controller->action->id == 'index')
            return;

        $files = Yii::$app->db->createCommand('SELECT * FROM {{%file}} file WHERE nid=:nid AND model=:model', [
                    ':nid' => $this->id,
                    ':model' => $this->model,
                ])->queryAll();


        foreach ($files as $file) {
            $this->owner->{$file['field']}[] = ['id' => $file['id'], 'filename' => $file['filename'], 'path' => $file['path'], 'mimetype' => $file['mimetype']];
        }
    }

    public function previewFiles($field, $control = false, $options = ['class' => 'file-preview-image']) {
        $this->_data();

        $out = [];
        $files = $this->owner->{$field};

        if (!isset($files[0]) || !$files[0]) {
            $files = [];
            $ff = Yii::$app->db->createCommand('SELECT * FROM {{%file}} file  WHERE nid=:nid AND model=:model AND field = :field', [
                        ':nid' => $this->id,
                        ':model' => $this->model,
                        ':field' => $field,
                    ])->queryAll();

            foreach ($ff as $file) {
                $files[] = ['id' => $file['id'], 'filename' => $file['filename'], 'path' => $file['path'], 'mimetype' => $file['mimetype']];
            }
        }


        if (empty($files))
            return [];

        foreach ($files as $k => $file) {
            if (strpos($file['mimetype'], "image") !== false)
                $img = Html::img('/' . $file['path'] . '/' . $file['filename'], $options);
            else
                $img = '
                <div class="file-preview-other">
                       <i class="glyphicon glyphicon-file"></i>
                   </div>
                   <div class="file-thumbnail-footer">
                    <div class="file-caption-name">' . $file['filename'] . '</div>

                </div>
                ';
            $del = '<div class="delete glyphicon glyphicon-trash"></div>';

            if ($control)
                $out[] = Html::a($img . $del, ['/site/delete-file', 'id' => $file['id'], 'hex' => md5(Yii::$app->request->cookieValidationKey . $_SERVER['REMOTE_ADDR'] . $file['filename'] . $file['id'])], ['class' => 'widget-file-remove', 'data-trigger' => strtolower($this->model . '-' . $field)]
                );
            else
                $out[] = $img;
        }

        return $out;
    }

}
