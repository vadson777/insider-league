<?php

if(!function_exists('validate')) {
	/**
	 * @param mixed $value
	 * @param string|array $rules
	 * @return bool
	 */
	function validate($value, $rules) : bool
    {
		return \Validator::make(['val' => $value], ['val' => $rules])->passes();
	}
}

if(!function_exists('poisson_distribution')) {
	/**
	 * Calculates Poisson distribution
	 * @param int $k
	 * @param float $mean
	 * @return float
	 */
	function poisson_distribution(int $k, float $mean) : float
	{
		$fact = gmp_intval(gmp_fact($k));
		return pow($mean, $k)*exp(-$mean)/$fact;
	}
}

if(!function_exists('cartesian_product')) {
	/**
	 * Computes the cartesian product of two or more arrays or traversable objects
	 * @param array|\Traversable $sequences
	 * @return array
	 */
	function cartesian_product($sequences) : array
	{
		$count = func_num_args();
		if($count > 1) {
			$sequences = func_get_args();
		}

		$product = [[]];
		foreach($sequences as $key => $values) {
			$newProduct = [];
			foreach($product as $vector) {
				foreach($values as $value) {
					$vector[$key] = $value;
					$newProduct[] = $vector;
				}
			}

			$product = $newProduct;
		}

		return $product;
	}
}