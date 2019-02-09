<?php
class ControllerProductCategory extends Controller {
	public function index() {
		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}


				$data['text_empty'] = $this->language->get('text_empty');
			
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		if (isset($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path . $url)
					);
				}
			}
		} else {
			$category_id = 0;
		}

		$category_info = $this->model_catalog_category->getCategory($category_id);

		if ($category_info) {
			$this->document->setTitle($category_info['meta_title']);
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);


            if (file_exists('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/octhemecategory/category.css')) {
                $this->document->addStyle('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/octhemecategory/category.css');
            } else {
                $this->document->addStyle('catalog/view/theme/default/stylesheet/octhemecategory/category.css');
            }

            $store_id = $this->config->get('config_store_id');
            if(isset($this->config->get('module_octhemeoption_custom_view')[$store_id])) {
                $data['use_custom_view'] = (int) $this->config->get('module_octhemeoption_custom_view')[$store_id];
            } else {
                $data['use_custom_view'] = 0;
            }

            if(isset($this->config->get('module_octhemeoption_category_view')[$store_id])) {
                $data['category_view'] = $this->config->get('module_octhemeoption_category_view')[$store_id];
            } else {
                $data['category_view'] = false;
            }

            if(isset($this->config->get('module_octhemeoption_category_view')[$store_id])) {
                $data['grid_columns'] = $this->config->get('module_octhemeoption_grid_columns')[$store_id];
            } else {
                $data['grid_columns'] = false;
            }

            if(isset($this->config->get('module_octhemeoption_use_layered')[$store_id])) {
                $data['use_layered'] = (int) $this->config->get('module_octhemeoption_use_layered')[$store_id];
            } else {
                $data['use_layered'] = 0;
            }

            if(isset($this->config->get('module_octhemeoption_use_cate_quickview')[$store_id])) {
	            $data['use_quickview'] = (int) $this->config->get('module_octhemeoption_use_cate_quickview')[$store_id];
	        } else {
	            $data['use_quickview'] = 0;
	        }

            if(isset($this->config->get('module_octhemeoption_image_effect')[$store_id])) {
                $data['image_effect'] = $this->config->get('module_octhemeoption_image_effect')[$store_id];
            } else {
                $data['image_effect'] = false;
            }

            if($data['image_effect'] == 'swatches') {
                $this->document->addScript('catalog/view/javascript/occolorswatches/swatches.js');
            }

            if($data['use_custom_view']) {
                $this->document->addScript('catalog/view/javascript/occategorygrid/grid.js');
            }
            
            if (!empty($_SERVER['HTTPS'])) {
                // SSL connection
                $base_url = str_replace('http://', 'https://', $this->config->get('config_url'));
            } else {
                $base_url = $this->config->get('config_url');
            }
            
            $data['ajax_sorts'] = array();

            $data['ajax_limits'] = array();

            if($data['use_layered']) {
                if (file_exists('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/octhemecategory/filter.css')) {
                    $this->document->addStyle('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/octhemecategory/filter.css');
                } else {
                    $this->document->addStyle('catalog/view/theme/default/stylesheet/octhemecategory/filter.css');
                }

                $this->document->addScript('catalog/view/javascript/occategorygrid/filter.js');

                $data['ajax_sorts'][] = array(
                    'text'  => $this->language->get('text_default'),
                    'value' => 'p.sort_order-ASC',
                    'href'  => $base_url . 'index.php?route=product/ocfilter/category&path=' . $category_id . '&sort=p.sort_order&order=ASC' . $url
                );

                $data['ajax_sorts'][] = array(
                    'text'  => $this->language->get('text_name_asc'),
                    'value' => 'pd.name-ASC',
                    'href'  => $base_url . 'index.php?route=product/ocfilter/category&path=' . $category_id . '&sort=pd.name&order=ASC' . $url
                );

                $data['ajax_sorts'][] = array(
                    'text'  => $this->language->get('text_name_desc'),
                    'value' => 'pd.name-DESC',
                    'href'  => $base_url . 'index.php?route=product/ocfilter/category&path=' . $category_id . '&sort=pd.name&order=DESC' . $url
                );

                $data['ajax_sorts'][] = array(
                    'text'  => $this->language->get('text_price_asc'),
                    'value' => 'p.price-ASC',
                    'href'  => $base_url . 'index.php?route=product/ocfilter/category&path=' . $category_id . '&sort=p.price&order=ASC' . $url
                );

                $data['ajax_sorts'][] = array(
                    'text'  => $this->language->get('text_price_desc'),
                    'value' => 'p.price-DESC',
                    'href'  => $base_url . 'index.php?route=product/ocfilter/category&path=' . $category_id . '&sort=p.price&order=DESC' . $url
                );

                if ($this->config->get('config_review_status')) {
                    $data['ajax_sorts'][] = array(
                        'text'  => $this->language->get('text_rating_desc'),
                        'value' => 'rating-DESC',
                        'href'  => $base_url . 'index.php?route=product/ocfilter/category&path=' . $category_id . '&sort=rating&order=DESC' . $url
                    );

                    $data['ajax_sorts'][] = array(
                        'text'  => $this->language->get('text_rating_asc'),
                        'value' => 'rating-ASC',
                        'href'  => $base_url . 'index.php?route=product/ocfilter/category&path=' . $category_id . '&sort=rating&order=ASC' . $url
                    );
                }

                $data['ajax_sorts'][] = array(
                    'text'  => $this->language->get('text_model_asc'),
                    'value' => 'p.model-ASC',
                    'href'  => $base_url . 'index.php?route=product/ocfilter/category&path=' . $category_id . '&sort=p.model&order=ASC' . $url
                );

                $data['ajax_sorts'][] = array(
                    'text'  => $this->language->get('text_model_desc'),
                    'value' => 'p.model-DESC',
                    'href'  => $base_url . 'index.php?route=product/ocfilter/category&path=' . $category_id . '&sort=p.model&order=DESC' . $url
                );

                $data['ajax_limits'] = array();

                $limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

                sort($limits);

                foreach($limits as $value) {
                    $data['ajax_limits'][] = array(
                        'text'  => $value,
                        'value' => $value,
                        'href'  => $base_url . 'index.php?route=product/ocfilter/category&path=' . $category_id . $url . '&limit=' . $value
                    );
                }

                $data['ajax_loader'] = $base_url . 'image/' . $this->config->get('module_octhemeoption_loader_img');
            }
            
			$data['heading_title'] = $category_info['name'];

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			// Set the last category breadcrumb
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
			);

			if ($category_info['image']) {

				if ( ($this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width') < 300) || ($this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height') < 300) ) {
				    $this->document->addOGMeta( 'property="og:image"', str_replace(' ', '%20', $this->model_tool_image->resize($category_info['image'], 300, 300)) );
					$this->document->addOGMeta('property="og:image:width"', '300');
					$this->document->addOGMeta('property="og:image:height"', '300');
				} else { 
				    $this->document->addOGMeta( 'property="og:image"', str_replace(' ', '%20', $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'))) );
					$this->document->addOGMeta('property="og:image:width"', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'));
					$this->document->addOGMeta('property="og:image:height"', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));
				}
                
				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));
			} else {

		    	$this->document->addOGMeta( 'property="og:image"', str_replace(' ', '%20', $this->model_tool_image->resize($this->config->get('config_logo'), 300, 300)) );
				$this->document->addOGMeta('property="og:image:width"', '300');
				$this->document->addOGMeta('property="og:image:height"', '300');
                
				$data['thumb'] = '';
			}

			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$data['compare'] = $this->url->link('product/compare');

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['categories'] = array();

			$results = $this->model_catalog_category->getCategories($category_id);


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
			
			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);

				$data['categories'][] = array(
					'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url)
				);
			}

			$data['products'] = array();

			$filter_data = array(
				'filter_category_id' => $category_id,
				'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);

			$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

			$results = $this->model_catalog_product->getProducts($filter_data);


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
			
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
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
				$pro_tags = array();

				if ($result['tag']) {
					$tags = explode(',', $result['tag']);

					foreach ($tags as $tag) {
						$pro_tags[] = array(
							'tag'  => trim($tag),
							'href' => $this->url->link('product/search', 'tag=' . trim($tag))
						);
					}
				}
				if ($result['quantity'] <= 0) {
					$stock = $result['stock_status'];
				} elseif ($this->config->get('config_stock_display')) {
					$stock = $result['quantity'];
				} else {
					$stock = $this->language->get('text_instock');
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
								
				
			

                if($data['image_effect'] == 'rotator') {
                    $this->load->model('catalog/ocproductrotator');

                    $product_rotator_image = $this->model_catalog_ocproductrotator->getProductRotatorImage($result['product_id']);

                    if($product_rotator_image) {
                        $rotate_image = $this->model_tool_image->resize($product_rotator_image, $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
                    } else {
                        $rotate_image = false;
                    }
                } else {
                    $rotate_image = false;
                }

                $swatches_images = array();

                $options = array();

                if($data['image_effect'] == 'swatches') {
                    $data['icon_swatches_width'] = $this->config->get('module_octhemeoption_cate_swatches_width')[$store_id];
                    $data['icon_swatches_height'] = $this->config->get('module_octhemeoption_cate_swatches_height')[$store_id];

                    $this->load->model('catalog/occolorswatches');

                    $images = $this->model_catalog_product->getProductImages($result['product_id']);

                    $is_swatches_option = false;

                    foreach ($images as $img) {
                        if ($img['product_option_value_id']) {
                            $image_option_id = $this->model_catalog_occolorswatches->getOptionIdByProductOptionValueId($img['product_option_value_id']);

                            if($image_option_id == $this->config->get('module_octhemeoption_swatches_option')[$store_id]) {
                                $is_swatches_option = true;

                                $swatches_images[] = array(
                                    'product_option_value_id' => $img['product_option_value_id'],
                                    'image' => $this->model_tool_image->resize($img['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'))
                                );
                            }
                        }
                    }

                    if($is_swatches_option) {
                        foreach ($this->model_catalog_product->getProductOptions($result['product_id']) as $option) {
                            if($option['option_id'] == $this->config->get('module_octhemeoption_swatches_option')[$store_id]) {
                                $product_option_value_data = array();

                                foreach ($option['product_option_value'] as $option_value) {
                                    if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
                                        $product_option_value_data[] = array(
                                            'product_option_value_id' => $option_value['product_option_value_id'],
                                            'option_value_id'         => $option_value['option_value_id'],
                                            'name'                    => $option_value['name'],
                                            'image'                   => $this->model_tool_image->resize($option_value['image'], $data['icon_swatches_width'], $data['icon_swatches_height']),
                                        );
                                    }
                                }

                                $options[] = array(
                                    'product_option_id'    => $option['product_option_id'],
                                    'product_option_value' => $product_option_value_data,
                                    'option_id'            => $option['option_id'],
                                    'name'                 => $option['name'],
                                    'type'                 => $option['type'],
                                    'value'                => $option['value'],
                                );
                            }
                        }
                    }
                }

            
				$data['products'][] = array(

                    'options' => $options,
                    'swatches_images' => $swatches_images,
                    'rotate_image' => $rotate_image,
            

				'is_new'      => $is_new,
				'rotator_image' => $rotator_image,
				'price_num'       => $price_num,
				'special_num'     => $special_num,
				'manufacturer' => $result['manufacturer'],
				'manufacturers' => $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id']),
			
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'model'       => $model,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'rate_special' => $rate_special,
					'tax'         => $tax,
					'stock'		=> $stock,
					'quantity'      => (int) $result['quantity'],
					'tags'		=> $pro_tags,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)
			);

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}


		    	$this->document->addOGMeta('property="og:url"', $this->url->link('product/category', 'path=' . $category_info['category_id'] . ( ($page != 1) ? '&page='. $page : '' ), true) );
                

            $ajax_pagination = new Pagination();
            $ajax_pagination->total = $product_total;
            $ajax_pagination->page = $page;
            $ajax_pagination->limit = $limit;
            $ajax_pagination->url = $base_url . 'index.php?route=product/ocfilter/category&path=' . $category_id . $url . '&page={page}';

            $data['ajax_pagination'] = $ajax_pagination->render();
            
			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'canonical');
			} else {
				$this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. $page), 'canonical');
			}
			
			if ($page > 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
			}

			if ($limit && ceil($product_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. ($page + 1)), 'next');
			}

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('product/category', $data));
		} else {
			$url = '';

			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
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
				'href' => $this->url->link('product/category', $url)
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
}
