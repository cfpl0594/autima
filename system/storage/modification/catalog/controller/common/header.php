<?php
class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');
		$data['powered'] = sprintf($this->language->get('text_powered'), $this->config->get('config_name'), date('Y', time()));
		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
		} else {
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		}

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['compare'] = $this->url->link('product/compare', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		

				// For page specific og tags
				if (isset($this->request->get['route'])) {
					if (isset($this->request->get['product_id'])) {
						$class = '-' . $this->request->get['product_id'];
						$this->document->addOGMeta('property="og:type"', 'product');
					} elseif (isset($this->request->get['path'])) {
						$class = '-' . $this->request->get['path'];
					} elseif (isset($this->request->get['manufacturer_id'])) {
						$class = '-' . $this->request->get['manufacturer_id'];
					} elseif (isset($this->request->get['information_id'])) {
						$class = '-' . $this->request->get['information_id'];
						$this->document->addOGMeta('property="og:type"', 'article');
					} else {
						$class = '';
					}
					$data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
				} else {
					$data['class'] = 'common-home';
					$this->document->addOGMeta('property="og:type"', 'website');
				}
				$this->load->model('tool/image');
				$data['logo_meta'] = str_replace(' ', '%20', $this->model_tool_image->resize($this->config->get('config_logo'), 300, 300));
				$data['ogmeta'] = $this->document->getOGMeta();
                
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		
				/* Edit for Search Category Module by OCMod */
				$module_status = $this->config->get('module_ocsearchcategory_status');
				if($module_status) {
					$data['search'] = $this->load->controller('extension/module/ocsearchcategory');
				} else {
					$data['search'] = $this->load->controller('common/search');
				}
				/* End Code */
			
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');


				$data['block1'] = $this->load->controller('common/block1');
				$data['block2'] = $this->load->controller('common/block2');
				$data['block3'] = $this->load->controller('common/block3');
				$data['block8'] = $this->load->controller('common/block8');
				$data['block9'] = $this->load->controller('common/block9');
				$data['block10'] = $this->load->controller('common/block10');		
				$data['block11'] = $this->load->controller('common/block11');	
				if($this->config->get('module_ocajaxlogin_status')){
					$data['use_ajax_login'] = true;
				}else{
					$data['use_ajax_login'] = false;
				}
				
				// For page specific css
				if (isset($this->request->get['route'])) {
					if (isset($this->request->get['product_id'])) {
						$class = '-' . $this->request->get['product_id'];
					} elseif (isset($this->request->get['path'])) {
						$class = '-' . $this->request->get['path'];
					} elseif (isset($this->request->get['manufacturer_id'])) {
						$class = '-' . $this->request->get['manufacturer_id'];
					} elseif (isset($this->request->get['information_id'])) {
						$class = '-' . $this->request->get['information_id'];
					} else {
						$class = '';
					}

					$data['class'] = str_replace('/', '-', $this->request->get['route']) . $class;
				} else {
					$data['class'] = 'common-home';
				}
			

        $data['store_id'] = $this->config->get('config_store_id');
        $data['theme_option_status'] = $this->config->get('module_octhemeoption_status');
        $data['a_tag'] = $this->config->get('module_octhemeoption_a_tag');
        $data['header_tag'] = $this->config->get('module_octhemeoption_header_tag');
        $data['body_css'] = $this->config->get('module_octhemeoption_body');
        
        if(isset($this->config->get('module_octhemeoption_quickview')[$data['store_id']])) {
            $module_quickview = (int) $this->config->get('module_octhemeoption_quickview')[$data['store_id']];
        } else {
            $module_quickview = 0;
        }

        if(isset($this->config->get('module_octhemeoption_use_cate_quickview')[$data['store_id']])) {
            $category_quickview = (int) $this->config->get('module_octhemeoption_use_cate_quickview')[$data['store_id']];
        } else {
            $category_quickview = 0;
        }

        if($module_quickview || $category_quickview) {
        	$data['use_quick_view'] = true;
        } else {
			$data['use_quick_view'] = false;
        }
        	
		return $this->load->view('common/header', $data);
	}
}
