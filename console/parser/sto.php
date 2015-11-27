<?php

namespace console\parser;

use Yii;

include dirname(__FILE__) . '/../parser/simple_html_dom.php';

//include '../../console/parser/simple_html_dom.php';

class Parser {

	public $html, $links, $domain, $objects_per_link;
	public $objects_links = [], $data = [];

	function __construct() {
		$this->domain = 'http://vse-sto.com.ua';
		$this->links['st'] = '/yalta/sto/';
		$this->links['mo'] = '/yalta/avtomoyki/';
		$this->links['sh'] = '/yalta/schinomontazhi/';
		$this->objects_per_type = 51; // кратность 17
	}

	public function getLinks() {
		return $this->links;
	}

	/*
	 * Отримання посилань на обьекти
	 */

	public function getObjectLinks($link, $type) {
		$this->objects_links = [];

		$this->html = file_get_html($this->domain . $link);

		$page = $this->getPagerNextLink($type);

		if ($page)
			return $this->getObjectLinksParse($link, $type, $page);

		return [];
	}

	/*
	 * Функція збору посилань(об'єктів) для парсингу
	 */

	public function getObjectLinksParse($link, $type, $page = '') {
		echo $this->domain . $link . $page . '<br>';
		$this->html = file_get_html($this->domain . $link . $page);

		foreach ($this->html->find('.catalog-list-item  a.item-link') as $itm) {

			/*
			 *  перевіряемо наявність посилання  в базі
			 *  перевіряемо чи не обновлювавася це посилання напротязі доби
			 *  якщо посилання відсутнє або обновлення було пізніше чим добу тому додаємо його в парсинг
			 */
			$href = parse_url($itm->href, PHP_URL_PATH);
			$id = Yii::$app->db->createCommand('SELECT id FROM {{objects_links}} WHERE domain=:domain AND link = :link LIMIT 1', [
						':domain' => $this->domain,
						':link' => $href,
							// ':updated' => (time()-86400), //доба
					])->queryScalar();


			if (!$id) {
				$this->objects_links[] = $href;
			}

			if (count($this->objects_links) >= $this->objects_per_type)
				return $this->objects_links;
		}

		/*
		 * Рекурсія якщо не назбирали достатньо повідомлень
		 */
		$page = $this->getPagerNextLink($type);
		if ($page)
			return $this->getObjectLinksParse($link, $type, $page);




		return $this->objects_links;
	}

	/*
	 * Отримання посилань на наступні(пейджінг) сторінки пошуку
	 */

	public function getPagerNextLink($type) {


		$pages = (new \yii\db\Query())
				->select('*')
				->from('pp')
				->all();

		$current_page = 1;
		$page_exist = 0;

		foreach ($pages as $pp) {
			if ($pp['type'] == $type) {
				$current_page = $pp['page'] + 1;
			}
		}


		$page_exist = $this->html->find('.pagination .step-links a[href=?page=' . $current_page . ']', 0);

		if ($current_page == 1 || $page_exist) {
			Yii::$app->db->createCommand()
					->update('pp', [
						'page' => $current_page
							], 'type = :type', [
						':type' => $type
					])->execute();
		}

		if ($page_exist)
			return $page_exist->href;
		elseif ($current_page == 1)
			return '?page=1';

		return '';

		return $this->html->find('.pagination .step-links span.current', 0)->next_sibling()->href;
	}

	/*
	 * Парсер типу пропозиції
	 */

	private function getType() {
		$this->data['type'] = 1;

		$d = @trim($this->html->find('h1 .category', 0)->plaintext);

		if ($d == 'СТО')
			$this->data['type'] = 1;
		if ($d == 'Шиномонтаж')
			$this->data['type'] = 2;
		if ($d == 'Автомойка')
			$this->data['type'] = 3;
	}

	/*
	 * Парсер Тайтлу
	 */

	private function getTitle() {
		$this->data['title'] = @trim($this->html->find('h1 .fn', 0)->plaintext);
	}

	/*
	 * Парсер опису
	 */

	private function getBody() {
		$this->data['body'] = '';
		$h3 = $this->html->find('h3');
		foreach ($h3 as $i) {
			if ($i->plaintext == 'Описание') {
				$this->data['body'] = $i->next_sibling()->innertext;
				return true;
			}
		}
	}

	/*
	 * Виды работ
	 */

	private function getServices() {
		$this->data['services'] = [];
		$data = $this->html->find('.object-info li strong');

		foreach ($data as $i) {
			if (strpos($i->plaintext, 'иды работ')) {
				//exit("AAA");
				$s = $i->next_sibling()->find('li a');
				foreach ($s as $itm) {
					$this->data['services'][] = trim($itm->plaintext);
				}
				return true;
			}
		}
	}

	/*
	 * Парсер Адреси
	 */

	private function getAddress() {
		$this->data['address'] = trim($this->html->find('.object-info .adr .street-address', 0)->plaintext);
	}

	/*
	 * Парсер
	 */

	private function getSite() {
		$this->data['site'] = '';
		$data = $this->html->find('.object-info a.url', 0);
		if ($data)
			$this->data['site'] = trim($data->href);
	}

	/*
	 * Парсер
	 */

	private function getPhone() {
		$this->data['phone'] = '';
		$data = $this->html->find('.object-info span.tel', 0);
		if ($data)
			$this->data['phone'] = trim($data->plaintext);
	}

	/*
	 * Авто
	 */

	private function getAvto() {
		$this->data['avto'] = [];
		$data = $this->html->find('.object-info li strong');

		foreach ($data as $i) {
			if (strpos($i->plaintext, 'пециализируется на о')) {
				$s = $i->parent()->find('a');
				foreach ($s as $itm) {
					$this->data['avto'][] = trim($itm->plaintext);
				}
				return true;
			}
		}
	}

	/*
	 * Тип мойки
	 */

	private function getMoikaType() {
		$this->data['moika_type'] = 1;
		$data = $this->html->find('.object-info li strong');

		foreach ($data as $i) {
			if (strpos($i->plaintext, 'ип мойки')) {
				$s = $i->parent()->plaintext;
				$p = strlen('Тип мойки:');
				$s = trim(substr($s, $p));
				if ($s == 'автоматическая')
					$this->data['moika_type'] = 2;

				return true;
			}
		}
	}

	/*
	 * Круглосуточная
	 */

	private function geth24() {
		$this->data['h24'] = 0;
		$data = $this->html->find('.object-info li strong');

		foreach ($data as $i) {
			if (strpos($i->plaintext, 'руглосуточная')) {
				$s = $i->parent()->plaintext;
				$p = strlen('Круглосуточная:');
				$s = trim(substr($s, $p));
				if ($s == 'да')
					$this->data['h24'] = 1;
				return true;
			}
		}
	}

	/*
	 * Парсер координат гугл
	 */

	private function getCoord() {
		$data = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address=' . str_replace(' ', '+', $this->data['address']) . '&language=ru&sensor=false'));
		$coordinates = $data->results[0]->geometry->location->lat . ',' . $data->results[0]->geometry->location->lng;
		$this->data['coordinates'] = $coordinates;
	}

	/*
	 * Парсер фото
	 */

	private function getImages() {
		$this->data['images'] = [];
		foreach ($this->html->find('.gallery_item a') as $itm) {
			$this->data['images'][] = $itm->href;
		}
	}

	/*
	 * Основна функція парсингу об'єкту
	 */

	public function getObject($link) {
		$this->data = [];
		$this->data['link'] = $link;
		$this->data['domain'] = $this->domain;

		$this->html = file_get_html($this->domain . $link);

		$data['images'] = [];
		$data['coordinates'] = NULL;

		/* --------------- */
		$this->getAddress();
		$this->getType();
		$this->getPhone();
		$this->getSite();
		$this->getTitle();
		$this->getBody();
		$this->getServices();
		$this->getMoikaType();
		$this->geth24();
		$this->getAvto();
		$this->getImages();

		/*



		 */
		return $this->data;
	}

}
