<?php

namespace ODB\DB\Operations;

trait Main
{
	use Where;
	use Cond;
	use parseWhere;
	use Other;
	use Functions;

	/**
	 * get count of rows for last select query
	 * @return int
	 */
	public function count()
	{
		$results = (array)$this->results();
		return isset($results[0]) ? count($this->_results) : 1;
	}
}
