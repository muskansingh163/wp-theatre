<?php

/**
 * Abstract Theater Item class.
 * 
 * @abstract
 * @since	0.16
 * @package	Theater/Abstracts
 */
abstract class Theater_Item {

	const name = 'undefined';	
	const post_type_name = 'undefined';

	/**
	 * ID of this item.
	 * 
	 * @since	0.16
	 * @var 	int
	 */
	var $ID;
	
	var $template;
	
	/**
	 * Sets the ID of this item.
	 * 
	 * Only happens if the optional `$default_args` parameter is set:
	 * <code>
	 * // Retrieve a list of upcoming dates.
	 * $dates = new Theater_Dates( array( 'start' => 'now' ) );
	 * </code>
	 * 
	 * @since	0.16
	 * @param 	int|WP_Post	$ID 	ID of post object or post object of this item.
	 * @return 	void
	 * @uses	Theater_Item::$ID to set the ID of this item.
	 */
	function __construct( $ID = false, $template = '' ) {
		
		if ( $ID instanceof WP_Post ) {
			$this->post = $ID;
			$ID = $ID->ID;
		}

		$this->ID = $ID;
		$this->template = $template;
		
	}

	/**
	 * Gets the HTML output for a field.
	 * 
	 * @uses 	Theater_Item::get_field_html() to get the HTML output for a field.
	 * @since	0.16
	 * @param 	string	$name	The field name.
	 * @return 	string			The field HTML output.
	 */
	function __get( $name ) {
		$value = $this->get_field_html($name);
		return $value;	
	}
	
	/**
	 * Gets the value for a field.
	 * 
	 * @uses	Theater_Item::get_field() to get the value for a field.
	 * @since	0.16
	 * @param	string	$name	The field name.
	 * @param 	array	$args	Not used.
	 * @return	mixed			The value for the field.
	 */
	function __call( $name, $args ) {

		// Handle deprecated usage of the $args['html'] param.
		if ( !empty ($args[0]['html']) ) {		
			if (!empty($args[0]['filters'])) {
				return $this->get_field_html( $name, $args[0]['filters'] );
			}
			return $this->get_field_html( $name );
		}
		
		// Handle deprecated usage of '{field}_html' item methods.
		$name_parts = explode('_', $name);
		if ('html' == $name_parts[count($name_parts) - 1]) {
			array_pop($name_parts);
			$name = implode('_', $name_parts);
			if (!empty($args[0])) {
				return $this->get_field_html( $name, $args[0] );
			}
			return $this->get_field_html( $name );
		}
		
		return $this->get_field( $name );
	}
	
	protected function apply_template_filters( $value, $filters ) {
		foreach ( $filters as $filter ) {
			$value = $filter->apply_to( $value, $this );
		}
		return $value;
	}

	/**
	 * Gets the value for a field.
	 * 
	 * @since	0.16
	 * @param 	string 	$name		The field name.
	 * @return	mixed
	 */
	abstract function get_field( $name );
	
	/**
	 * Gets the HTML output for a field.
	 * 
	 * @since	0.16
	 * @param 	string 	$name		The field name.
	 * @param 	array 	$filters 	(default: array())
	 * @return	string				The HTML output for a field.
	 */
	abstract function get_field_html( $name, $filters = array() );
	
	abstract function get_html();
	
	function get_name() {
		return static::name;
	}

	function get_post_type() {
		return static::post_type_name;
	}

	/**
	 * @deprecated	0.16
	 */
	function html( $template = '' ) {
		if ( is_array( $template ) ) {
			$defaults = array(
				'template' => '',
			);
			$args = wp_parse_args( $template, $defaults );
			$template = $args['template'];
		}

		$this->template = $template;
		return $this->get_html();
	}
	
	/**
	 * @deprecated	0.4	
	 */
	function compile() {
		return $this->html();
	}

	/**
	 * @deprecated	0.4	
	 */
	function render() {
		echo $this->html();
	}

	/**
	 * @deprecated	0.16
	 */
	function post_class() {
		$classes = array();
		$classes[] = static::post_type_name;
		return implode( ' ',$classes );
	}

}

?>