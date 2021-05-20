<?php

namespace MPHB\Admin\Fields;

class VariablePricesField extends ComplexHorizontalField {

	const TYPE = 'variable-prices';

	protected function getTableClasses() {
		return 'mphb-table-centered';
	}

	protected function renderDeleteItemButton( $atts = '', $classes = '' ) {
		return '<a href="#" class="mphb-complex-delete-item ' . $classes . '" data-id="' . $this->uniqid . '" ' . $atts . '>' . esc_html( $this->deleteLabel ) . '</a>';
	}

}
