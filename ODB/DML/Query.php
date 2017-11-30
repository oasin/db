<?php

namespace ODB\DB\DML;

trait Query
{
	/**
	 * DB::query()
	 * check if sql statement is prepare
	 * append value for sql statement if $params is set
	 * fetch results
	 * @param string $sql
	 * @param array $params
	 * @return mixed
	 */
	public function query($sql, $params = [])
	{
		// uncomment this line to see your query
		//var_dump($sql);
		$this->_lastQuery = $sql;
		$this->_query = "";
		$this->_where = "WHERE";
		
		$this->_error = false;
		// check if sql statement is prepared
		$query = $this->_pdo->prepare($sql);
		// if $params isset
		if(count($params)) {
			/**
			 * @var $x int
			 * counter
			 */
			$x = 1;
			foreach($params as $param) {
				// append values to sql statement
				$query->bindValue($x, $param);

				$x++;
			}
		}

		// check if sql statement executed
		if($query->execute())
		{
			try
			{
				$this->_results = $query->fetchAll(\config('fetch'));
			}
			catch (\PDOException $e) {}

			$this->_sql = $query;
			
			// set _count = count rows
			$this->_count = $query->rowCount();

		}
		else
			$this->_error = true;


		return $this;
	}

	public function rawQuery($sql){

		return $this->query($sql);
	}

	/**
	 * select from database
	 * @param  array  $fields fields we need to select
	 * @return Collect the result of select as a Collection object
	 */
	public function select($fields = ['*'], $last = false)
	{
		if($fields === true)
		{
			$fields = ['*'];
			$last = true;
		}
		if($fields != ['*'] && !is_null($this->_idColumn))
		{
			if(!in_array($this->_idColumn, $fields))
			{
				$fields[$this->_idColumn];
			}
		}

		if(!$last)
			$sql = "SELECT " . implode(', ', $fields)
				. " FROM {$this->_table} {$this->_query}";
		else
		{
			
			$sql = "SELECT * FROM (
                        SELECT " . implode(', ', $fields) . "  
                        FROM {$this->_table}
                        
                         {$this->_query}  
                        ) sub  ORDER by id ASC";
		}


		$this->_query = $sql;
		$this->_ordering = false;

		return $this->collection([
			'results' => $this->query($sql)->results(),
			'table'   => $this->_table,
			'id'      => $this->_idColumn
		]);

	
	}

	/**
	 * find a single row from table via id
	 * @param  int $id [description]
	 * @return Collection or object (as your choice from config file)  results or empty
	 */
	public function find($id)
	{
		return $this->where($this->_idColumn, $id)
			->first();
	}

	/**
	 * Get First record Only
	 */
	public function first()
	{
		$results = $this->select()->results();

		if(count((array)$results))
		{
			return $this->collection([
				'results' => $results[0],
				'table'   => $this->_table,
				'id'      => $this->_idColumn
			]);
		}

		return $this->collection([
			'results' => [],
			'table'   => $this->_table,
			'id'      => $this->_idColumn
		]);
	}

	/**
	 * find records by columns
	 * USING :
	 * $db->findBy('username', 'ali')->first(); // or select() or paginate()
	 * @param $column
	 * @param $value
	 * @return mixed
	 */
	public function findBy($column, $value)
	{
		return $this->where($column, $value);
	}
}
