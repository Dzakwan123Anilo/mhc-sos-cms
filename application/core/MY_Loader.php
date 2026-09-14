<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Loader.php";

class MY_Loader extends MX_Loader
{
	/**
	 * Backward-compatible alias used by MX_Loader 5.5.
	 *
	 * CodeIgniter 3.1.13 renamed the internal helper to
	 * _ci_prepare_view_vars(). Keep the MX method working without
	 * modifying the vendored third-party library.
	 *
	 * @param mixed $vars
	 * @return array
	 */
	protected function _ci_object_to_array($vars)
	{
		return $this->_ci_prepare_view_vars($vars);
	}
}
