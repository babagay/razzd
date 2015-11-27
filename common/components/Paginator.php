<?php

    namespace common\components;

    use Yii;

    use yii\base\InvalidConfigException;

    use yii\helpers\Html;

    use yii\base\Widget;

    use yii\data\Pagination;
    use yii\widgets\LinkPager;

    class Paginator extends LinkPager
    {

        /**
         * Basic url prefix
         * @var string
         */
        public $url = "";

        /**
         * Classname to wrap the box in
         * @var null
         */
        public $wrapperClass = null;


        /**
         * Disable highlightion of active link
         * @var bool
         */
        public $disableHighlightActive = false;

        /**
         * Hide 'prev' and 'next' arrows
         * @var bool
         */
        public $noArrows = false;

        public function init()
        {

            parent::init();

        }

        /**
         * @override
         */
        public function run()
        {

            if ($this->registerLinkTags) {
                $this->registerLinkTags();
            }

            if(!is_null($this->wrapperClass)) {
                if($this->pagination->totalCount <= $this->pagination->defaultPageSize){
                    echo "<div class=\"{$this->wrapperClass}\">" . $this->renderSinglePageButton() . "</div>";
                } else {
                    echo "<div class=\"{$this->wrapperClass}\">" . $this->renderPageButtons() . "</div>";
                }
            } else {
                if($this->pagination->totalCount <= $this->pagination->defaultPageSize){
                    echo $this->renderSinglePageButton();
                } else {
                    echo $this->renderPageButtons();
                }
            }

        }

        protected function renderSinglePageButton(){

            $class = isset($this->options['class']) ? $this->options['class'] : '';

            return "<ul class=\"$class\">" . Html::tag('li', Html::a($label = '1', $this->url."1", $this->linkOptions), $options = []) . "</ul>";
        }

        protected function renderPageButton($label, $page, $class, $disabled, $active)
        {
            $options = ['class' => $class === '' ? null : $class];

            if(!$this->disableHighlightActive)
            if ($active) {
                Html::addCssClass($options, $this->activePageCssClass);
            }

            if ($disabled) {
                Html::addCssClass($options, $this->disabledPageCssClass);

                if($this->noArrows)
                    return ;

                return Html::tag('li', Html::tag('span', $label), $options);
            }

            if((string)$label == '')
                if($this->noArrows)
                    return;

            $linkOptions = $this->linkOptions;

            $linkOptions['data-page'] = $page;

            //$linkOptions['onclick'] = 'submit_form(' . $page . ')';

            $linkPage = $page + 1;

            return Html::tag('li', Html::a($label, $this->url.$linkPage, $linkOptions), $options);
        }

    }