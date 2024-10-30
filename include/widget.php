<?php
/* Widget management */

if ( ! defined( 'ABSPATH' ) ) { 
    exit;
}

class inboxphoto_widget extends WP_Widget {

	function __construct() {
		$widget_ops = array( 
			'classname' => 'inboxphoto_widget',
			'description' => __( 'inbox.photo widget', 'inboxphoto' ),
		);
		parent::__construct( 'inboxphoto_widget', __('inbox.photo widget', 'inboxphoto'), $widget_ops );
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$category = apply_filters( 'widget_title', $instance['category'] );
		$product = apply_filters( 'widget_title', $instance['product'] );
		$text = apply_filters( 'widget_title', $instance['text'] );
		$slug = get_option( 'inbox_photo_slug' );
		$currency = get_option( 'inbox_photo_currency' );
		$order_url = 'https://inbox.photo/shop/'.$slug.'/app/'.$category.'/';

		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$url = 'https://'.$slug.'.inbox.photo/api/'.$category.'/'.$product.'/';
		$request = wp_remote_get($url, array(
			'method' => 'GET',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array("Content-type: application/json"),
			'cookies' => array()
			)
		);
		$response = wp_remote_retrieve_body($request);
		$products = json_decode($response);
		$type = $products->type;
		switch ($type) {
			case 'prints':
			case 'large_prints':
				$name = __('Prints', 'inboxphoto');
				$widget = '<div itemscope itemtype="http://schema.org/Product">';
				if ( ! empty ( $products->product->custom_image)) $widget .= '<a href="'. $order_url .'"><img itemprop="image" src="'. $products->product->custom_image .'" alt="'. $name .'" /></a>';
				$widget .= '<p itemprop="name">'.$name.'</p>';
				$widget .= '<meta itemprop="category" content="prints" />';
//				$widget .= '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">'. __('Price:','inboxphoto') .' <span itemprop="price" content="'. $price .'">'. $price .'</span> <span itemprop="priceCurrency" content="'. $currency .'">'. $currency .'</span><meta itemprop="availability" href="http://schema.org/InStock" content="In stock" /></div>';
				$widget .= '<a href="'. $order_url .'"><button class="inboxphotobutton">'.$text.'</button></a>';
				break;
			case 'collages':
			case 'canvas_prints':
			case 'photo_gifts':
			case 'photo_books':
			case 'cards':
			case 'calendars':
				$order_url .=  $product.'/';
				$name = $products->product->name;
				$price = $products->product->unit_price;
				$widget = '<div itemscope itemtype="http://schema.org/Product">';
				if ( ! empty ( $products->product->custom_image)) $widget .= '<a href="'. $order_url .'"><img itemprop="image" src="'. $products->product->custom_image .'" alt="'. $name .'" /></a>';
				$widget .= '<p itemprop="name">'.$name.'</p>';
				if ( $type = 'calendars' ) $widget .= '<meta itemprop="category" content="calendar" />';
				if ( $type = 'cards' ) $widget .= '<meta itemprop="category" content="card" />';
				if ( $type = 'photo_books' ) $widget .= '<meta itemprop="category" content="photobook" />';
				$widget .= '<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">'. __('Price:','inboxphoto') .' <span itemprop="price" content="'. $price .'">'. $price .'</span> <span itemprop="priceCurrency" content="'. $currency .'">'. $currency .'</span><meta itemprop="availability" href="http://schema.org/InStock" content="In stock" /></div>';
				$widget .= '<a href="'. $order_url .'"><button class="inboxphotobutton">'.$text.'</button></a>';
				break;
		}

		echo $widget;
		echo $args['after_widget'];
	}

	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'inboxphoto' );
		}
		if ( isset( $instance[ 'category' ] ) ) {
			$category = $instance[ 'category' ];
		}
		else {
//			$category = __( 'New category', 'inboxphoto' );
		}
		if ( isset( $instance[ 'product' ] ) ) {
			$product = $instance[ 'product' ];
		}
		else {
			$product = __( 'New product', 'inboxphoto' );
		}
		if ( isset( $instance[ 'text' ] ) ) {
			$text = $instance[ 'text' ];
		}
		else {
			$text = __( 'New text', 'inboxphoto' );
		}
		$slug = get_option( 'inbox_photo_slug' );
		$categories_url = 'https://'.$slug.'.inbox.photo/api/list/';
		$request = wp_remote_get($categories_url, array(
			'method' => 'GET',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array("Content-type: application/json"),
			'cookies' => array()
			)
		);
		$response = wp_remote_retrieve_body($request);

		$random = rand();
		if ( $category ) {
			switch ($type) {
				case 'prints':
				case 'large_prints':
					$widget = '<p>'. __('Product not supported.', 'inboxphoto' ) .'</p>';
					break;
				case 'collages':
				case 'canvas_prints':
				case 'photo_gifts':
				case 'photo_books':
				case 'cards':
			}

			echo '<p>'. __('Current setting:', 'inboxphoto' ) .'<br />'. __( 'Category slug:', 'inboxphoto' ) .' '.$category.'<br />'. __( 'Product id:', 'inboxphoto' ) .' ' . $product.'</p>';
		}
		echo '<p><label for="'.$this->get_field_id( 'title' ) .'">'. __( 'Title:', 'inboxphoto' ) .'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" type="text" value="'.esc_attr( $title ).'" /></p>';

		echo '<input class="widefat" id="'.$this->get_field_id( 'category' ).'" name="'.$this->get_field_name( 'category' ).'" inboxphoto-tag="inboxphoto-widget-'.$random.'-category" type="hidden" value="'.esc_attr( $category ).'" />';
		echo '<input class="widefat" id="'.$this->get_field_id( 'type' ).'" name="'.$this->get_field_name( 'type' ).'"  type="hidden" value="'.esc_attr( $type ).'" />';
		echo '<p>'. __('Change to category:', 'inboxphoto').' ';	
		echo '<select name="inboxphoto-widget-'.$random.'-category-select" id="inboxphoto-widget-'.$random.'-category-select">';
		echo '</select>';
		echo '</p>';

		echo '<input class="widefat" id="'.$this->get_field_id( 'product' ).'" name="'.$this->get_field_name( 'product' ).'" inboxphoto-tag="inboxphoto-widget-'.$random.'-product" type="hidden" value="'.esc_attr( $product ).'" />';
		echo '<p>'. __('Change to product:', 'inboxphoto').' ';	
		echo '<select name="inboxphoto-widget-'.$random.'-product-select" id="inboxphoto-widget-'.$random.'-product-select">';
		echo '<option>'. __('Select category first', 'inboxphoto').'</option>';
		echo '</select>';
		echo '</p>';

		echo '<p><label for="'.$this->get_field_id( 'text' ) .'">'. __( 'Button text:', 'inboxphoto' ) .'</label>';
		echo '<input class="widefat" id="'.$this->get_field_id( 'text' ).'" name="'.$this->get_field_name( 'text' ).'" type="text" value="'.esc_attr( $text ).'" /></p>';
		?>
		<script>
		var jsonData = {"categories":<?php echo $response ?>};
		
		function InboxPhotoWidgetAddCategoriesList()
		{
			var categoryselect = jQuery('#inboxphoto-widget-<?php echo $random ?>-category-select');
			categoryselect.append('<option value=""><?php echo ( __( 'Select a category', 'inboxphoto' ) ) ?></option>');
			for (var i = 0; i < jsonData.categories.length; i++) {
				if (jsonData.categories[i].type != 'prints' && jsonData.categories[i].type != 'large_prints') {
					jQuery('#inboxphoto-widget-<?php echo $random ?>-category-select').append('<option value="'+jsonData.categories[i].slug+'">'+jsonData.categories[i].title+'</option>');
				}
			}
			return true;
		}

		jQuery(function() {
			jQuery("#inboxphoto-widget-<?php echo $random ?>-category-select").change(function() {
				var category = jQuery('#inboxphoto-widget-<?php echo $random ?>-category-select').val();
				jQuery('[inboxphoto-tag=inboxphoto-widget-<?php echo $random ?>-category]').attr('value', category);
				var productselect = jQuery('#inboxphoto-widget-<?php echo $random ?>-product-select');
				productselect.find('option').remove().end();
				productselect.append('<option value=""><?php echo ( __( 'Select a product', 'inboxphoto' ) ) ?></option>');
				for (var i = 0; i < jsonData.categories.length; i++) {	
					if (jsonData.categories[i].type == 'prints' || jsonData.categories[i].type == 'large_prints') {
					}
					else {
						if (jsonData.categories[i].slug == category) {
							for (var j = 0; j < jsonData.categories[i].products.length; j++) {
								productselect.append('<option value="'+jsonData.categories[i].products[j].id+'">'+jsonData.categories[i].products[j].name+'</option>');
							}
						}
					}
				}
			})

			jQuery("#inboxphoto-widget-<?php echo $random ?>-product-select").change(function() {
				var product = jQuery('#inboxphoto-widget-<?php echo $random ?>-product-select').val();
				jQuery('[inboxphoto-tag=inboxphoto-widget-<?php echo $random ?>-product]').attr('value', product);
			})
		});
		
		InboxPhotoWidgetAddCategoriesList();
		</script>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['category'] = ( ! empty( $new_instance['category'] ) ) ? strip_tags( $new_instance['category'] ) : '';
		$instance['product'] = ( ! empty( $new_instance['product'] ) ) ? strip_tags( $new_instance['product'] ) : '';
		$instance['text'] = ( ! empty( $new_instance['text'] ) ) ? strip_tags( $new_instance['text'] ) : '';
		$instance['type'] = ( ! empty( $new_instance['type'] ) ) ? strip_tags( $new_instance['type'] ) : '';
		return $instance;
	}
}

function inboxphoto_load_widget() {
	register_widget( 'inboxphoto_widget' );
}
add_action( 'widgets_init', 'inboxphoto_load_widget' );