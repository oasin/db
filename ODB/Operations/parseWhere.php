<?php

namespace ODB\DB\Operations;

trait parseWhere {
	private $__whereTypes = ['AND', 'OR'];
	/**
	 * How to use
	 * $con = [
	 *  [
	 *      'sex', '=', 'female'
	 *  ],
	 * 'AND' => [
	 *      'position', '=', 'manager'
	 *  ]
	 * ];
	 * $db->table('table_name')->parseWhere($con)->select();
	 */
	public function parseWhere(array $cons, $type = "AND")
	{

		$this->_query .= " {$type} (";

		foreach ($cons as $con => $st)
		{
			if(is_array($st))
			{
				if(!is_numeric($st[2]))
					$st[2] = "'$st[2]'";
				else
					$st[2] = "`$st[2]`";

				if (strtolower($con) === 'none' || $con === 0)
				{
					$this->_query .= " `{$st[0]}` $st[1] $st[2] ";
				}
				else
				{
					if ($this->con($con))
					{
						$this->_query .= " {$con} `{$st[0]}` $st[1] $st[2] ";
					}
				}
			}
			else
			{
				$this->_query .= " `{$cons[0]}` $cons[1] $cons[2] ";
				break;
			}
			
		}

		$this->_query .= ')';

		return $this;
	}

	private function con($con)
	{
		return in_array(strtoupper($con), $this->__whereTypes);
	}
}
