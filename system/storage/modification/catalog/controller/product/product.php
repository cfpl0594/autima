<?php
class ControllerProductProduct extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('product/product');

		$data['breadcrumbs'] = array();

				$data['block11'] = $this->load->controller('common/block11');
			

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$this->load->model('catalog/category');

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path)
					);
				}
			}

			// Set the last category breadcrumb
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$url = '';

				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}

				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}

				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}

				if (isset($this->request->get['limit'])) {
					$url .= '&limit=' . $this->request->get['limit'];
				}

				$data['breadcrumbs'][] = array(
					'text' => $category_info['name'],
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url)
				);
			}
		}

		$this->load->model('catalog/manufacturer');

		if (isset($this->request->get['manufacturer_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_brand'),
				'href' => $this->url->link('product/manufacturer')
			);

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {
				$data['breadcrumbs'][] = array(
					'text' => $manufacturer_info['name'],
					'href' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $this->request->get['manufacturer_id'] . $url)
				);
			}
		}

		if (isset($this->request->get['search']) || isset($this->request->get['tag'])) {
			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_search'),
				'href' => $this->url->link('product/search', $url)
			);
		}

		if (isset($this->request->get['product_id'])) {
			$product_id = (int)$this->request->get['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

            $this->load->model('catalog/category');

            $pull_category = $this->model_catalog_product->getCategories($this->request->get['product_id']);

            $cat_ids = '';
						if(!empty($pull_category)){
							foreach ($pull_category as $catid) {
								if ($catid =='0') {
									unset($catid);
								}
								if ($catid['category_id'] !=='0'){
									$cat_ids .= $catid['category_id'].'_';
								}
							}

              $path        = '';
              $parts       = array(explode('_', (string)$cat_ids));
        			$flip_array  = array_pop($parts);
        			$category_id = array_reverse($flip_array);

        			//skip 0, which would be the root category
              if(isset($category_id[1])){
        				$par_category = $this->model_catalog_category->getParent($category_id[1]);

        	      if(!isset($this->request->get['path'])){
        					if (isset($par_category)) {
        						$url = '';
        					}

        					$data['breadcrumbs'] = array();

				$data['block11'] = $this->load->controller('common/block11');
			

        					$data['breadcrumbs'][] = array(
        						'text' => $this->language->get('text_home'),
        						'href' => $this->url->link('common/home')
        					);

        					if(isset($category_id[1])){
        						$category_info = $this->model_catalog_category->getCategory($category_id[1]);

        							if(isset($category_info)){
        								$categoryParent = isset($par_category['category_id']) ? $par_category['category_id'] : '0';

        								$data['breadcrumbs'][] = array(
        									'text'      => isset($category_info['name']) ? $category_info['name'] : '',
        									'href'      => $this->url->link('product/category', 'path=' . $category_id[1]),
        									'separator' => $this->language->get('text_separator')
        								);
        							}
        					}
              	}
            	}
        		}
          

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $product_info['name'],
				'href' => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id'])
			);

			$this->document->setTitle($product_info['meta_title']);
			$this->document->setDescription($product_info['meta_description']);
			$this->document->setKeywords($product_info['meta_keyword']);
			$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');

		    	$this->document->addOGMeta('property="og:url"', $this->url->link('product/product', 'product_id=' . $this->request->get['product_id']) );
                
			$this->document->addScript('catalog/view/javascript/jquery/magnific/jquery.magnific-popup.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/magnific/magnific-popup.css');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment-with-locales.min.js');
			$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css');


            /* Zoom & Swatches */
            $store_id = $this->config->get('config_store_id');
            $use_swatches = (int) $this->config->get('module_octhemeoption_use_swatches')[$store_id];
            $use_zoom = (int) $this->config->get('module_octhemeoption_use_zoom')[$store_id];
            
			$data['heading_title'] = $product_info['name'];

			$data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));

			$this->load->model('catalog/review');

			$data['tab_review'] = sprintf($this->language->get('tab_review'), $product_info['reviews']);

			$data['product_id'] = (int)$this->request->get['product_id'];
			$data['manufacturer'] = $product_info['manufacturer'];
			$data['manufacturers'] = $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$data['model'] = $product_info['model'];
			$data['reward'] = $product_info['reward'];
			$data['points'] = $product_info['points'];
			$data['description'] = html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8');

				$data['short_description'] = utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..';
			
			$data['short_description'] = utf8_substr(trim(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'))),0,$this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..';
			
			if ($product_info['quantity'] <= 0) {
				$data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$data['stock'] = $product_info['quantity'];
			} else {
				$data['stock'] = $this->language->get('text_instock');
			}

			$this->load->model('tool/image');

			if ($product_info['image']) {
				$data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
			} else {
				$data['popup'] = '';
			}

			if ($product_info['image']) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height'));
			} else {
				$data['thumb'] = '';
			}


            if($use_swatches) {
                $data['use_swatches'] = true;
                $data['icon_swatches_width'] = $this->config->get('module_octhemeoption_swatches_width')[$store_id];
                $data['icon_swatches_height'] = $this->config->get('module_octhemeoption_swatches_height')[$store_id];
                $data['swatches_option'] = $this->config->get('module_octhemeoption_swatches_option')[$store_id];

                if (file_exists('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/occolorswatches/swatches.css')) {
                    $this->document->addStyle('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/occolorswatches/swatches.css');
                } else {
                    $this->document->addStyle('catalog/view/theme/default/stylesheet/occolorswatches/swatches.css');
                }

                $this->document->addScript('catalog/view/javascript/occolorswatches/swatches.js');
            } else {
                $data['use_swatches'] = false;
            }
            
            if($use_zoom) {
                $data['use_zoom'] = true;

                if ($product_info['image']) {
                    $data['small_image'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'));
                } else {
                    $data['small_image'] = '';
                }

                if (file_exists('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/oczoom/zoom.css')) {
                    $this->document->addStyle('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/oczoom/zoom.css');
                } else {
                    $this->document->addStyle('catalog/view/theme/default/stylesheet/oczoom/zoom.css');
                }

                $data['popup_dimension'] = array(
                    'width' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'),
                    'height' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')
                );

                $data['thumb_dimension'] = array(
                    'width' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'),
                    'height' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height')
                );

                $data['small_dimension'] = array(
                    'width' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'),
                    'height' => $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height')
                );

                $bg_status = (int) $this->config->get('module_octhemeoption_zoom_background_status')[$store_id];
                if($bg_status) {
                    $zoom_bg_status = true;
                } else {
                    $zoom_bg_status = false;
                }

                $title_status = (int) $this->config->get('module_octhemeoption_zoom_title')[$store_id];
                if($title_status) {
                    $zoom_title_status = true;
                } else {
                    $zoom_title_status = false;
                }

                $data['zoom_config'] = array(
                    'position' => $this->config->get('module_octhemeoption_zoom_position')[$store_id],
                    'space' => $this->config->get('module_octhemeoption_zoom_space')[$store_id],
                    'bg_status' => $zoom_bg_status,
                    'bg_color' => '#' . $this->config->get('module_octhemeoption_zoom_background_color')[$store_id],
                    'bg_opacity' => $this->config->get('module_octhemeoption_zoom_background_opacity')[$store_id],
                    'title_status' => $zoom_title_status
                );

                $this->document->addStyle('catalog/view/javascript/cloudzoom/css/cloud-zoom.css');
                $this->document->addScript('catalog/view/javascript/cloudzoom/cloud-zoom.1.0.2.min.js');
                $this->document->addScript('catalog/view/javascript/cloudzoom/zoom.js');
            } else {
                $data['use_zoom'] = false;
            }

            
			$data['images'] = array();

			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);

				if ($product_info['image']) {
					$this->document->addOGMeta('property="og:image"', str_replace(' ', '%20', $this->model_tool_image->resize($product_info['image'], 600, 315)) );
					$this->document->addOGMeta('property="og:image:width"', '600');
					$this->document->addOGMeta('property="og:image:height"', '315');
			    } else {
		    		$this->document->addOGMeta( 'property="og:image"', str_replace(' ', '%20', $this->model_tool_image->resize($this->config->get('config_logo'), 300, 300)) );
					$this->document->addOGMeta('property="og:image:width"', '300');
					$this->document->addOGMeta('property="og:image:height"', '300');
	     		}
				foreach ($results as $result) {
			    	$this->document->addOGMeta( 'property="og:image"', str_replace(' ', '%20', $this->model_tool_image->resize($result['image'], 600, 315)) );
					$this->document->addOGMeta('property="og:image:width"', '600');
					$this->document->addOGMeta('property="og:image:height"', '315');
       			}
                

			foreach ($results as $result) {
				$data['images'][] = array(

                    'product_option_value_id' => $result['product_option_value_id'],
                    'product_image_option' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_thumb_height')),
            
					'popup' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height')),
					'thumb' => $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_additional_height'))
				);
			}

			if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
				$data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
			} else {
				$data['price'] = false;
			}

			if ((float)$product_info['special']) {
				$data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				
			} else {
				$data['special'] = false;
				
			}

			if ($this->config->get('config_tax')) {
				$data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
			} else {
				$data['tax'] = false;
			}

			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);

			$data['discounts'] = array();

			foreach ($discounts as $discount) {
				$data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency'])
				);
			}

			$data['options'] = array();

			foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) {
				$product_option_value_data = array();

				foreach ($option['product_option_value'] as $option_value) {
					if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
						if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
							$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax') ? 'P' : false), $this->session->data['currency']);
						} else {
							$price = false;
						}

						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
							'price'                   => $price,
							'price_prefix'            => $option_value['price_prefix']
						);
					}
				}

				$data['options'][] = array(
					'product_option_id'    => $option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $option['option_id'],
					'name'                 => $option['name'],
					'type'                 => $option['type'],
					'value'                => $option['value'],
					'required'             => $option['required']
				);
			}


				$meta_price = ( $data['special'] != false) ? $data['special'] : $data['price'] ;
				$meta_price = trim(trim(($data['special'] != false) ? $data['special'] : $data['price'], $this->currency->getSymbolLeft($this->session->data['currency'])), $this->currency->getSymbolRight($this->session->data['currency']));
				$decimal_point_meta_price = $this->language->get('decimal_point') ? $this->language->get('decimal_point') : '.';
                $thousand_point_meta_price = $this->language->get('thousand_point')? $this->language->get('thousand_point') : ' ';
                $meta_price = str_replace($thousand_point_meta_price, '', $meta_price);
                if ( $decimal_point_meta_price != '.' ) {
                  $meta_price = str_replace($decimal_point_meta_price, '.', $meta_price);
                }
                $meta_price = number_format((float)$meta_price, 2, '.', '');
				
				$this->document->addOGMeta('property="product:price:amount"', $meta_price);
				$this->document->addOGMeta('property="product:price:currency"', $this->session->data['currency']);
                
			if ($product_info['minimum']) {
				$data['minimum'] = $product_info['minimum'];
			} else {
				$data['minimum'] = 1;
			}

			$data['review_status'] = $this->config->get('config_review_status');

			if ($this->config->get('config_review_guest') || $this->customer->isLogged()) {
				$data['review_guest'] = true;
			} else {
				$data['review_guest'] = false;
			}

			if ($this->customer->isLogged()) {
				$data['customer_name'] = $this->customer->getFirstName() . '&nbsp;' . $this->customer->getLastName();
			} else {
				$data['customer_name'] = '';
			}

			$data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$data['rating'] = (int)$product_info['rating'];

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'));
			} else {
				$data['captcha'] = '';
			}

			$data['share'] = $this->url->link('product/product', 'product_id=' . (int)$this->request->get['product_id']);

			$data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);

			$data['products'] = array();


				/* Get new product */
				$this->load->model('catalog/product');
		
				$data['new_products'] = array();
		
				$filter_data = array(
					'sort'  => 'p.date_added',
					'order' => 'DESC',
					'start' => 0,
					'limit' => 10
				);
		
				$new_results = $this->model_catalog_product->getProducts($filter_data);
				/* End */
			
			$results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
			
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_related_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$rate_special = round(100 - ((float)$result['special'] / $result['price'] * 100));
				} else {
					$special = false;
					$rate_special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}
				$model = $result['model'];
				
				$data['tags'] = array();
				if ($result['tag']) {
					$tags = explode(',', $result['tag']);

					foreach ($tags as $tag) {
						$data['tags'][] = array(
							'tag'  => trim($tag),
							'href' => $this->url->link('product/search', 'tag=' . trim($tag))
						);
					}
				}
				
				
				

				$is_new = false;
				if ($new_results) { 
					foreach($new_results as $new_r) {
						if($result['product_id'] == $new_r['product_id']) {
							$is_new = true;
						}
					}
				}
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price_num = $this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'));
				} else {
					$price_num = false;
				}

				if ((float)$result['special']) {
					$special_num = $this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax'));
				} else {
					$special_num = false;
				}
				/// Product Rotator /
				$store_id = $this->config->get('config_store_id');

				$product_rotator_status = (int) $this->config->get('module_octhemeoption_rotator')[$store_id];
				if($product_rotator_status == 1) {
				 $this->load->model('catalog/ocproductrotator');
				 $this->load->model('tool/image');
			 
				 $product_id = $result['product_id'];
				 $product_rotator_image = $this->model_catalog_ocproductrotator->getProductRotatorImage($product_id);
			 
				 if($product_rotator_image) {
				  $rotator_image = $this->model_tool_image->resize($product_rotator_image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height')); 
				 } else {
				  $rotator_image = false;
				 }
				} else {
				 $rotator_image = false;    
				}
				/// End Product Rotator /	
				
				$data['use_quickview'] = (int) $this->config->get('module_octhemeoption_quickview')[$store_id];
				
				
			
				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'model'       => $model,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'rate_special' => $rate_special,
					'tax'         => $tax,
					'tags'  		=>	$data['tags'],
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $rating,

				'is_new'      => $is_new,
				'rotator_image' => $rotator_image,
				'price_num'       => $price_num,
				'special_num'     => $special_num,
				'manufacturer' => $result['manufacturer'],
				'manufacturers' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']),
			
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			$data['tags'] = array();

			if ($product_info['tag']) {
				$tags = explode(',', $product_info['tag']);

				foreach ($tags as $tag) {
					$data['tags'][] = array(
						'tag'  => trim($tag),
						'href' => $this->url->link('product/search', 'tag=' . trim($tag))
					);
				}
			}

			$data['recurrings'] = $this->model_catalog_product->getProfiles($this->request->get['product_id']);

			$this->model_catalog_product->updateViewed($this->request->get['product_id']);
			
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/product', $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . $this->request->get['search'];
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . $this->request->get['tag'];
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('product/product', $url . '&product_id=' . $product_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function review() {
		$this->load->language('product/product');

		$this->load->model('catalog/review');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);

		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * 5) + 1 : 0, ((($page - 1) * 5) > ($review_total - 5)) ? $review_total : ((($page - 1) * 5) + 5), $review_total, ceil($review_total / 5));

		$this->response->setOutput($this->load->view('product/review', $data));
	}

	public function write() {
		$this->load->language('product/product');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}

			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating']) || $this->request->post['rating'] < 0 || $this->request->post['rating'] > 5) {
				$json['error'] = $this->language->get('error_rating');
			}

			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('review', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$json['error'] = $captcha;
				}
			}

			if (!isset($json['error'])) {
				$this->load->model('catalog/review');

				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);

				$json['success'] = $this->language->get('text_success');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getRecurringDescription() {
		$this->load->language('product/product');
		$this->load->model('catalog/product');

            $this->load->model('catalog/category');

            $pull_category = $this->model_catalog_product->getCategories($this->request->get['product_id']);

            $cat_ids = '';
						if(!empty($pull_category)){
							foreach ($pull_category as $catid) {
								if ($catid =='0') {
									unset($catid);
								}
								if ($catid['category_id'] !=='0'){
									$cat_ids .= $catid['category_id'].'_';
								}
							}

              $path        = '';
              $parts       = array(explode('_', (string)$cat_ids));
        			$flip_array  = array_pop($parts);
        			$category_id = array_reverse($flip_array);

        			//skip 0, which would be the root category
              if(isset($category_id[1])){
        				$par_category = $this->model_catalog_category->getParent($category_id[1]);

        	      if(!isset($this->request->get['path'])){
        					if (isset($par_category)) {
        						$url = '';
        					}

        					$data['breadcrumbs'] = array();

				$data['block11'] = $this->load->controller('common/block11');
			

        					$data['breadcrumbs'][] = array(
        						'text' => $this->language->get('text_home'),
        						'href' => $this->url->link('common/home')
        					);

        					if(isset($category_id[1])){
        						$category_info = $this->model_catalog_category->getCategory($category_id[1]);

        							if(isset($category_info)){
        								$categoryParent = isset($par_category['category_id']) ? $par_category['category_id'] : '0';

        								$data['breadcrumbs'][] = array(
        									'text'      => isset($category_info['name']) ? $category_info['name'] : '',
        									'href'      => $this->url->link('product/category', 'path=' . $category_id[1]),
        									'separator' => $this->language->get('text_separator')
        								);
        							}
        					}
              	}
            	}
        		}
          

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		if (isset($this->request->post['recurring_id'])) {
			$recurring_id = $this->request->post['recurring_id'];
		} else {
			$recurring_id = 0;
		}

		if (isset($this->request->post['quantity'])) {
			$quantity = $this->request->post['quantity'];
		} else {
			$quantity = 1;
		}

		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		$recurring_info = $this->model_catalog_product->getProfile($product_id, $recurring_id);

		$json = array();

		if ($product_info && $recurring_info) {
			if (!$json) {
				$frequencies = array(
					'day'        => $this->language->get('text_day'),
					'week'       => $this->language->get('text_week'),
					'semi_month' => $this->language->get('text_semi_month'),
					'month'      => $this->language->get('text_month'),
					'year'       => $this->language->get('text_year'),
				);

				if ($recurring_info['trial_status'] == 1) {
					$price = $this->currency->format($this->tax->calculate($recurring_info['trial_price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					$trial_text = sprintf($this->language->get('text_trial_description'), $price, $recurring_info['trial_cycle'], $frequencies[$recurring_info['trial_frequency']], $recurring_info['trial_duration']) . ' ';
				} else {
					$trial_text = '';
				}

				$price = $this->currency->format($this->tax->calculate($recurring_info['price'] * $quantity, $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);

				if ($recurring_info['duration']) {
					$text = $trial_text . sprintf($this->language->get('text_payment_description'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				} else {
					$text = $trial_text . sprintf($this->language->get('text_payment_cancel'), $price, $recurring_info['cycle'], $frequencies[$recurring_info['frequency']], $recurring_info['duration']);
				}

				$json['success'] = $text;
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
