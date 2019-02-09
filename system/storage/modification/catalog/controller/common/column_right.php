<?php
class ControllerCommonColumnRight extends Controller {
	public function index() {
		$this->load->model('design/layout');

		if (isset($this->request->get['route'])) {
			$route = (string)$this->request->get['route'];
		} else {
			$route = 'common/home';
		}

		$layout_id = 0;


       	if ($route == 'product/category') {
			$store_id = $this->config->get('config_store_id');

			if(isset($this->config->get('module_octhemeoption_use_layered')[$store_id])) {
				$use_layered = (int) $this->config->get('module_octhemeoption_use_layered')[$store_id];
			} else {
				$use_layered = 0;
			}

			if(isset($this->config->get('module_octhemeoption_layered_column')[$store_id])) {
				$column_side = $this->config->get('module_octhemeoption_layered_column')[$store_id];
			} else {
				$column_side = false;
			}

			if($use_layered && $column_side == 'right') {
				$data['use_layered'] = true;
			} else {
				$data['use_layered'] = false;
			}
			
			if($data['use_layered']) {
				$data['layered_section'] = $this->load->controller('product/ocfilter');
			}
		} else {
			$data['use_layered'] = false;
		}
            
		if ($route == 'product/category' && isset($this->request->get['path'])) {
			$this->load->model('catalog/category');

			$path = explode('_', (string)$this->request->get['path']);

			$layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
		}

		if ($route == 'product/product' && isset($this->request->get['product_id'])) {
			$this->load->model('catalog/product');

			$layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
		}

		if ($route == 'information/information' && isset($this->request->get['information_id'])) {
			$this->load->model('catalog/information');

			$layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get['information_id']);
		}

		if (!$layout_id) {
			$layout_id = $this->model_design_layout->getLayout($route);
		}

		if (!$layout_id) {
			$layout_id = $this->config->get('config_layout_id');
		}

		$this->load->model('setting/module');

		$data['modules'] = array();

		$modules = $this->model_design_layout->getLayoutModules($layout_id, 'column_right');

		foreach ($modules as $module) {
			$part = explode('.', $module['code']);

			if (isset($part[0]) && $this->config->get('module_' . $part[0] . '_status')) {
				$module_data = $this->load->controller('extension/module/' . $part[0]);

				if ($module_data) {
					$data['modules'][] = $module_data;
				}
			}

			if (isset($part[1])) {
				$setting_info = $this->model_setting_module->getModule($part[1]);

				if ($setting_info && $setting_info['status']) {
					$output = $this->load->controller('extension/module/' . $part[0], $setting_info);

					if ($output) {
						$data['modules'][] = $output;
					}
				}
			}
		}

		return $this->load->view('common/column_right', $data);
	}
}
