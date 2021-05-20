<?php

namespace MPHB\Admin\Fields;

class VariablePriceField extends InputField {

	const TYPE = 'variable-price';

	protected $min = 0;
	protected $step = 0.01;
	protected $variablePrices = null;

	public function __construct( $name, $details, $value = '' ){
		parent::__construct( $name, $details, $value );

		$this->min  = isset( $details['min'] ) ? $details['min'] : $this->min;
		$this->step = isset( $details['step'] ) ? $details['step'] : $this->step;

		$this->default = is_numeric( $this->default ) ? max( $this->min, $this->default ) : $this->min;
		$this->default = array(
			'base'				 => (float)$this->default,
			'enable_variations'	 => "0", // "0"|"1"
			'variations'		 => array()
		);

		$this->variablePrices = $this->buildVariations();

		$this->setValue( $value );
	}

	private function buildVariations(){
		$minAdults   = MPHB()->settings()->main()->getMinAdults();
		$maxAdults   = MPHB()->settings()->main()->getSearchMaxAdults();
		$minChildren = MPHB()->settings()->main()->getMinChildren();
		$maxChildren = MPHB()->settings()->main()->getSearchMaxChildren();

		$variablePrices = FieldFactory::create( $this->getName() . '[variations]', array(
			'type'		 => 'variable-prices',
			'add_label'	 => __( 'Add Variation', 'motopress-hotel-booking' ),
			'fields'	 => array(
				FieldFactory::create( 'adults', array(
					'type'		 => 'number',
					'label'		 => __( 'Adults', 'motopress-hotel-booking' ),
					'default'	 => '',
					'min'		 => $minAdults,
					'max'		 => $maxAdults,
					'step'		 => 1
				) ),
				FieldFactory::create( 'children', array(
					'type'		 => 'number',
					'label'		 => __( 'Children', 'motopress-hotel-booking' ),
					'default'	 => '',
					'min'		 => $minChildren,
					'max'		 => $maxChildren,
					'step'		 => 1
				) ),
				FieldFactory::create( 'price', array(
					'type'		 => 'number',
					'label'		 => __( 'Price', 'motopress-hotel-booking' ),
					'default'	 => 0,
					'min'		 => 0,
					'step'		 => 0.01,
					'size'		 => 'price'
				) )
			)
		) );

		return $variablePrices;
	}

	protected function getCtrlClasses(){
		// "mphb-left" for complex tables to align inputs by the left side
		return parent::getCtrlClasses() . ' mphb-left';
	}

	protected function generateAttrs(){
		$atts = parent::generateAttrs();

		$atts .= ' min="' . esc_attr( $this->min ) . '"';
		$atts .= ' step="' . esc_attr( $this->step ) . '"';

		return $atts;
	}

	public function setValue( $value ){
		if ( is_array( $value ) ) {
			$this->value = array_merge( $this->default, $value );
		} else if ( is_numeric( $value ) ) {
			$this->value = $this->default;
			$this->value['base'] = (float)$value;
		} else {
			$this->value = $this->default;
		}

		$this->variablePrices->setValue( $this->value['variations'] );
	}

	public function setName( $name ){
		$this->name = $name;
		$this->variablePrices->setName( $name . '[variations]' );
	}

	protected function renderInput(){
		$value = $this->value;

		$result = '';

		// Base price input
		$result .= __( 'Base price', 'motopress-hotel-booking' );
		$result .= ' <input name="' . esc_attr( $this->getName() ) . '[base]" value="' . esc_attr( $value['base'] ) . '" id="' . MPHB()->addPrefix( $this->getName() ) . '[base]" class="mphb-price-text" type="number"' . $this->generateAttrs() . ' />';

		if ( $this->required ) {
			$result .= '<strong><abbr title="required">*</abbr></strong>';
		}

		$result .= '<br />';

		// Enable variations checkbox
		$result .= '<input name="' . esc_attr( $this->getName() ) . '[enable_variations]" value="0" id="' . MPHB()->addPrefix( $this->getName() ) . '[enable_variations]-hidden" type="hidden" />';
		$result .= '<label><input name="' . esc_attr( $this->getName() ) . '[enable_variations]" value="1" id="' . MPHB()->addPrefix( $this->getName() ) . '[enable_variations]" type="checkbox" ' . checked( '1', $value['enable_variations'], false ) . ' class="mphb-enable-variable-pricing" /> ' . __( 'Enable variable pricing', 'motopress-hotel-booking' ) . '</label>';

		// Variable prices table
		if ( !$value['enable_variations'] ) {
			// Add class for a new row in a complex field
			$this->variablePrices->addClass( 'mphb-hide' );
		} else {
			// Remove class after previous row in a complex field
			$this->variablePrices->removeClass( 'mphb-hide' );
		}
		$result .= $this->variablePrices->render();

		return $result;
	}

	public function sanitize( $value ){
		$base = isset( $value['base'] ) ? sanitize_text_field( $value['base'] ) : $this->default['base'];
		$base = is_numeric( $base ) ? max( $this->min, floatval( $base ) ) : $this->default['base'];

		$enableVariations = isset( $value['enable_variations'] ) ? sanitize_text_field( $value['enable_variations'] ) : $this->default['enable_variations'];

		$variations = isset( $value['variations'] ) ? $value['variations'] : array();
		$this->variablePrices->sanitize( $variations );

		return array(
			'base'				 => $base,
			'enable_variations'	 => $enableVariations,
			'variations'		 => $variations
		);
	}

}
