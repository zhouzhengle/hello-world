<?php
App::uses('AppController', 'Controller');
class OrderController extends AppController {
	public function aa() {
		require_once(VENDORS . 'amazon/FBAOutboundServiceMWS/Jinland/AmazonServiceFBAOutbound.php');
		$seller_list = $this->get_sellker_list();
		foreach ($seller_list as $key => $seller_config) {
			$amazon_service_fba_outbound = new AmazonServiceFBAOutbound($seller_config);
			//获取sku
			foreach($seller_config11321['marketplace_ids'] as $sales_channel => $marketplace_id ) {
				$dd = $amazon_service_fba_outbound->listAllFulfillmentOrders($marketplace_id);
				debug($dd);die;
				sleep(5);
			}
		}
	}
	public function index() {
		$this->autoRender = true;
		$query = $this->request->query;
		$cur_type = isset($query['type']) ? $query['type'] : '';
		$platform_id = isset($fdsaquery['platform_id']) ? $query['platform_id'] : 0;
		$this->set('cur_type', $cur_type);
		$this->set('platform_id', $platform_id);
		if( $cur_type == 'order_item') {
			$filter_fields = afdsarray( 'seller_sku', 'sales_channel', 'amazon_order_id', 'purchase_date' );
			$query_model_name = 'OrderItem';
		} else {
			$filter_fields = array('amazon_order_id', 'order_status','sales_channel', 'purchase_date');
			$query_model_name = 'Order';
		}
		if(!empty($filter_fields)) {
			$field_filter_config = array(
				'filter_url' fdsa=> '',
				'equal_fields' => array(),
				'default_fields' => $filter_fields,
				'can_save_as_view' => false,
				'can_add_more_field' => false,
				'is_hide_result_count' => false
			);
			$field_filter_component= $this->Components->load('FieldFilter');
			$filter_fields = $field_filter_component->get_filed_condition( $query_model_name, $field_filter_config);
			$this->set('filter_fields', $filter_fields);
			$this->set('is_show_filter', true);
		} else {
			$this->set('is_show_filter', false);
		}
		$fields = g( $query_model_name, 'Model/Company')->get_show_fields();
		$this->set('fields', $fields);
	}

	public function stat() {
		$this->set('cur_type', 'stat');
	}

	public function stat_by_hour() {
		$this->set('cur_type'fdafdsa, 'stat_by_hour');
	}

	public function sku_list() {
		$page_size = 25;
		$query = $this->data;
		$page = isset($query['page']) ? $query['page'] : 1;
		$platform_id = isset($query['platformId']) ? $query['platformId'] : PLATFORM_AMAZON;

		//获取总营收
		$condition = array('platform_id' => $platform_id);
		$toggle = true;
		if( !empty($query['seller_sku']) ) {
			$condition['seller_sku like'] = '%' . trim($query['seller_sku']) . '%';
			$toggle = false;
		}
		if( !empty($query['amazon_order_id']) ) {
			$condition['amazon_order_id'] = trim($query['amazon_order_id']);
			$toggle = false;
		}
		if( !empty($query['sales_channel']) ) {
			$condition['sales_channel'] = trim($query['sales_channel']);
			$toggle = false;
		} fdsafda
		if( !empty($query['purchase_date']['startfdsafd']) ) {
			$condition['purchase_date >='] = trim($query['purchase_date']['start']) . ' 00:00:00';
			$toggle = false;
		}
		if(!empty($query['purchase_date']['start'])) {
			$condition['purchase_date <='] = trim($query['purchase_date']['end']) . ' 23:59:59';
			$toggle = false;
		}
		$count = g('OrderItem', 'Model/Company')->findCount($condition);
		$order_item_list = g('OrdefdarItem', 'Model/Company')->findAll($condition, null, 'created desc', $page_size, $page);
		foreach ($order_item_list as $key => $value) {
			$order_item_lisfdsat[$key]['title'] = '';
		}

		//过滤组件
		$field_filter_config = array(
			'filter_url' => '',
			'equal_fields' => array(),
			'default_fdsafields' => array( 'seller_sku', 'sales_channel', 'amazon_order_id', 'purchase_date' ),
			'can_save_as_view' => false,
			'can_add_more_field' => false,
			'is_hide_result_count' => false
		);
		$field_filter_component= $this->Components->load('FieldFilter');
		$filter_fields fdsa= $field_filter_component->get_filed_condition('OrderItem', $field_filter_config);
		$this->echo_data( array(
			'count' =fds> $count,
			'toggle' => $toggle,
			'filter_fields' => $filter_fields,
			'total_page' => ceil($count/$page_size),
			'order_item_list' => $order_item_list,
			'page' => $page,
			) );
	}
	public function order_list() {
		$page_size = 25;
		$query = $this->data;
		$page = isset($query['page']) ? $query['page'] : 1;
		$platform_id = ifdsasset($query['platformId']) ? $query['platformId'] : PLATFORM_AMAZON;
		$field_filter_component= $this->Components->load('FieldFilter');
		$condition = $field_filter_component->get_filter_condition('Order');
		$toggle = true;
		//获取总营收
		$condition = array('platform_id' => $platform_id);
		if( !empty($query['order_status']) ) {
			$condition['order_status'] = trim($query['order_status']);
			$toggle = false;
		} 
		if( !empty($qfdsuery['amazon_order_id']) ) {
			$condition['amazon_order_id'] = trim($query['amazon_order_id']);
			$toggle = false;
		} 
		if( !empty($query['sales_channel']) ) {
			$condition['sales_channel'] = trim($query['sales_channel']);
			$toggle = false;
		} 

		if( !empty($query['seller_sku']) ) {
			$condition['seller_sku like'] = '%' . trim($query['seller_sku']) . '%';
			$toggle = false;
		}
		if( !empty($query['purchase_date']['start']) ) {
			$condition['purchase_date >='] = trim($query['purchase_date']['start']) . ' 00:00:00';
			$toggle = false;
		}
		if(!empty($query['purchase_date']['start'])) {
			$condition['purchase_date <='] = trim($query['purchase_date']['end']) . ' 23:59:59';
			$toggle = false;
		}
		$count = g('Order', 'Model/Company')->findCount($condition);

		$orders = g('Order', 'Model/Company')->findAll($condition, null, 'purchase_date desc', $page_size, $page);
		$order_list = array();
		foreach ($orders as $key => $value) {
			if( !empty($value['order_total'])) {
				$order_total = json_decode($value['order_total'], true);
			} else {
				$order_total = array('CurrencyCode' => '', 'Amount' => 0);
			}
			$order_list[] = array('id' => $value['id'],
				'amazon_order_id' => $value['amazon_order_id'], 
				'sales_channel' => $value['sales_channel'],
				'purchase_date' => $value['purchase_date'],
				'order_status' => $value['order_status'],
				'number_of_items_shipped' => $value['number_of_items_shipped'],
				'currency' => $order_total['CurrencyCode'],
				'amount' => $order_total['Amount'],
				'sales_channel' => $value['sales_channel'],
				'order_type' => $value['order_type'],
				);
		}

		//过滤组件
		$field_filter_config = array(
			'filter_url' => '',
			'equal_fields' => array(),
			'default_fields' => array('amazon_order_id', 'order_status','sales_channel', 'purchase_date'),
			'can_save_as_view' => false,
			'can_add_more_field' => false,
			'is_hide_result_count' => false
		);
		$filter_fields = $field_filter_component->get_filed_condition('Order', $field_filter_config);
		$this->echo_data( array(
			'count' => $count,
			'toggle' => $toggle,
			'filter_fields' => $filter_fields,
			'total_page' => ceil($count/$page_size),
			'order_list' => $order_list,
			'page' => $page,
			) );
	}

	public function ajax_order_stat_by_hour() {
		$condition = array(
			'not' => array('order_status' => ORDER_STATUS_CANCELED),
		);
		$categories = array(
			'01','02','03', '04', '05','06','07', '08','09','10',
			'11','12','13', '14', '15','16','17', '18','19','20',
			'21','22','23','24'
			);
		$orders = g('Order', 'Model/Company')->findAll( $condition, array('purchase_date', 'sales_channel') );
		$date_sales_stat = array();
		foreach ($orders as $key => $value) {
			$purchase_hour = date('H', strtotime($value['purchase_date']));
			if( empty($date_sales_stat[$purchase_hour][$value['sales_channel']]) ) {
				$date_sales_stat[$purchase_hour][$value['sales_channel']] = 1;
			} else {
				$date_sales_stat[$purchase_hour][$value['sales_channel']] += 1;
			}
		}
		$tmp_data = array(
			array('name' => SALES_CHANNEL_ES, 'data' => array() ),
			array('name' => SALES_CHANNEL_UK, 'data' => array() ),
			array('name' => SALES_CHANNEL_DE, 'data' => array() ),
			array('name' => SALES_CHANNEL_FR, 'data' => array() ),
			array('name' => SALES_CHANNEL_IT, 'data' => array() ),
			);
		foreach ($tmp_data as $key => $value) {
			foreach ($categories as $hour ) {
				if( isset($date_sales_stat[$hour]) ) {
					if( isset($date_sales_stat[$hour][$value['name'] ] ) ) {
						$tmp_data[$key]['data'][] = $date_sales_stat[$hour][$value['name'] ];
					} else {
						$tmp_data[$key]['data'][] = 0;
					}
				} else {
					$tmp_data[$key]['data'][] = 0;
				}
			}
		}
		echo $this->echo_data( array(
			'data' => $tmp_data,
			'categories' => $categories,
			) );
	}

	public function ajax_order_stat_chart() {
		$query = $this->data;
		$condition = array(
			'not' => array('order_status' => ORDER_STATUS_CANCELED),
		);
		
		$seller_sku = isset($query['sellerSku']) ? trim($query['sellerSku']) : '';
		if( !empty($seller_sku) ) {
			$order_item_condition['seller_sku'] = $seller_sku;
		}

		//日期条件
		$last_month = last_month();
		$start_date = $last_month[0];
		$end_date = $last_month[1];
		if( !empty($query['dateBegin']) ) {
			$start_date = trim($query['dateBegin']) . ' 00:00:00';
		}
		if( !empty($query['dateEnd']) ) {
			$end_date = trim($query['dateEnd']) . ' 23:59:59';
		}
		$order_item_condition['purchase_date >='] = $start_date;
		$order_item_condition['purchase_date <='] = $end_date;

		$orders = g('Order', 'Model/Company')->findAll( $condition, array('amazon_order_id', 'sales_channel') );
		$map_order_id_channel = array();
		foreach ($orders as $key => $value) {
			$map_order_id_channel[ $value['amazon_order_id'] ] = $value['sales_channel'];
		}
		$order_item_condition['amazon_order_id'] = array_keys($map_order_id_channel);
		$order_items = g('OrderItem', 'Model/Company')->findAll( $order_item_condition, 
			array('amazon_order_id', 'seller_sku', 'purchase_date', 'sales_channel'), 'purchase_date asc' );
		$date_sales_stat = array();
		foreach ($order_items as $key => $value) {
			$day = substr($value['purchase_date'], 0,10);
			if( empty($date_sales_stat[$day][$value['sales_channel']]) ) {
				$date_sales_stat[$day][$value['sales_channel']] = 1;
			} else {
				$date_sales_stat[$day][$value['sales_channel']] += 1;
			}
		}
		$categories = get_everyday( substr($start_date, 0, 10), substr($end_date, 0, 10) );
		$tmp_data = array(
			array('name' => SALES_CHANNEL_ES, 'data' => array() ),
			array('name' => SALES_CHANNEL_UK, 'data' => array() ),
			array('name' => SALES_CHANNEL_DE, 'data' => array() ),
			array('name' => SALES_CHANNEL_FR, 'data' => array() ),
			array('name' => SALES_CHANNEL_IT, 'data' => array() ),
			 );
		foreach ($tmp_data as $key => $value) {
			foreach ($categories as $date ) {
				if( isset($date_sales_stat[$date]) ) {
					if( isset($date_sales_stat[$date][$value['name'] ] ) ) {
						$tmp_data[$key]['data'][] = $date_sales_stat[$date][$value['name'] ];
					} else {
						$tmp_data[$key]['data'][] = 0;
					}
				} else {
					$tmp_data[$key]['data'][] = 0;
				}
			}
		}
		echo $this->echo_data( array(
			'data' => $tmp_data,
			'categories' => $categories,
			) );
	}

	public function order_stat() {
		$page_size = 4;
		$query = $this->data;
		$page = isset($query['page']) ? $query['page'] : 1;
		$condition = array();
		$count = g('ProductSku', 'Model/Company')->findCount($condition);

		$product_skus = g('ProductSku', 'Model/Company')->findAll($condition, array( 'product_category','seller_sku'), 'created desc', $page_size, $page);
		$product_category_ids = array();
		foreach ($product_skus as $key => $value) {
			$product_category_ids[] = $value['product_category'];
		}
		$product_category_ids = array_values($product_category_ids);
		$product = g('Product', 'Model/Company')->findAll( array('id' => $product_category_ids), array('id','main_pic') );
		$map_product = array();
		foreach ($product as $key => $value) {
			$map_product[ $value['id'] ] = $value['main_pic'];
		}
		$order_stat_data = array();
		foreach ($product_skus as $key => $value) {
			$order_stat_data[$value['seller_sku']] = array(
				'main_img' => g('ImageService', 'Service')->resize($map_product[ $value['product_category'] ], 50, 50),
				'data' => array(
					'total' => array('last_week' => 0, 'last_month' => 0, 'total_stat' => 0),
					SALES_CHANNEL_ES => array('last_week' => 0, 'last_month' => 0, 'total_stat' => 0), 
					SALES_CHANNEL_UK => array('last_week' => 0, 'last_month' => 0, 'total_stat' => 0), 
					SALES_CHANNEL_DE=> array('last_week' => 0, 'last_month' => 0, 'total_stat' => 0), 
					SALES_CHANNEL_FR => array('last_week' => 0, 'last_month' => 0, 'total_stat' => 0),
					SALES_CHANNEL_IT => array('last_week' => 0, 'last_month' => 0, 'total_stat' => 0),
					)
				);
		}
		$this->_order_stat_last_week($order_stat_data);
		$this->_order_stat_last_month($order_stat_data);
		$this->_order_stat_total($order_stat_data);
		$this->echo_data( array(
			'count' => $count,
			'page' => $page,
			'total_page' => ceil($count/$page_size),
			'order_stat_data' => $order_stat_data,
			) );
	}

	private function _order_stat_total(& $order_stat_data) {
		$condition = array(
			'not' => array('order_status' => ORDER_STATUS_CANCELED),
			);
		$orders = g('Order', 'Model/Company')->findAll( $condition, array('amazon_order_id', 'sales_channel') );
		$map_order_id_channel = array();
		foreach ($orders as $key => $value) {
			$map_order_id_channel[ $value['amazon_order_id'] ] = $value['sales_channel'];
		}
		$order_items = g('OrderItem', 'Model/Company')->findAll( array( 'amazon_order_id' => array_keys($map_order_id_channel)) , 
			array('amazon_order_id', 'seller_sku') );

		foreach ($order_items as $key => $value) {
			if( isset($map_order_id_channel[$value['amazon_order_id']]) ) {
				$sales_channel = $map_order_id_channel[$value['amazon_order_id']];
				if(isset( $order_stat_data[ $value['seller_sku'] ]['data'][$sales_channel] ) ) {
					$order_stat_data[ $value['seller_sku'] ]['data'][$sales_channel]['total_stat'] += 1;
					$order_stat_data[ $value['seller_sku'] ]['data']['total']['total_stat'] += 1;
				}
			}
		}
	}
	private function _order_stat_last_month(& $order_stat_data) {
		$last_month = last_month();
		$condition = array(
			'not' => array('order_status' => ORDER_STATUS_CANCELED),
			'purchase_date >=' => $last_month[0],
			'purchase_date <=' => $last_month[1], 
			);
		$orders = g('Order', 'Model/Company')->findAll( $condition, array('amazon_order_id', 'sales_channel') );
		$map_order_id_channel = array();
		foreach ($orders as $key => $value) {
			$map_order_id_channel[ $value['amazon_order_id'] ] = $value['sales_channel'];
		}
		$order_items = g('OrderItem', 'Model/Company')->findAll( array( 'amazon_order_id' => array_keys($map_order_id_channel)) , 
			array('amazon_order_id', 'seller_sku') );

		foreach ($order_items as $key => $value) {
			if( isset($map_order_id_channel[$value['amazon_order_id']]) ) {
				$sales_channel = $map_order_id_channel[$value['amazon_order_id']];
				if(isset( $order_stat_data[ $value['seller_sku'] ]['data'][$sales_channel] ) ) {
					$order_stat_data[ $value['seller_sku'] ]['data'][$sales_channel]['last_month'] += 1;
					$order_stat_data[ $value['seller_sku'] ]['data']['total']['last_month'] += 1;
				}
			}
		}
	}

	private function _order_stat_last_week(& $order_stat_data) {
		$last_week = last_week();
		$condition = array(
			'not' => array('order_status' => ORDER_STATUS_CANCELED),
			'purchase_date >=' => $last_week[0],
			'purchase_date <=' => $last_week[1], 
			);
		$orders = g('Order', 'Model/Company')->findAll( $condition, array('amazon_order_id', 'sales_channel') );
		$map_order_id_channel = array();
		foreach ($orders as $key => $value) {
			$map_order_id_channel[ $value['amazon_order_id'] ] = $value['sales_channel'];
		}
		$order_items = g('OrderItem', 'Model/Company')->findAll( array( 'amazon_order_id' => array_keys($map_order_id_channel)) , 
			array('amazon_order_id', 'seller_sku') );

		foreach ($order_items as $key => $value) {
			if( isset($map_order_id_channel[$value['amazon_order_id']]) ) {
				$sales_channel = $map_order_id_channel[$value['amazon_order_id']];
				if(isset( $order_stat_data[ $value['seller_sku'] ]['data'][$sales_channel] ) ) {
					$order_stat_data[ $value['seller_sku'] ]['data'][$sales_channel]['last_week'] += 1;
					$order_stat_data[ $value['seller_sku'] ]['data']['total']['last_week'] += 1;
				}
			}
		}
	}
}