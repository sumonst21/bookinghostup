<?php

namespace WTS_EAE\Modules\TestimonialSlider;

use WTS_EAE\Base\Module_Base;

class Module extends Module_Base {

	public function get_widgets() {
		return [
			'TestimonialSlider',
		];
	}

	public function get_name() {
		return 'eae-testimonialslider';
	}

}