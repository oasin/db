<?php

/**
 * Relation between tables
 *
 * this part is cumming soon
 */

/*****************************************************************************
 *
 * How to use :
 * $db = ODB\DB\Database::connect();
 * $db->table("blog")->join("comments", ["comments.id", "=", blog.id], "left");
 *
 * sql = SELECT * FROM blog LEFT JOIN comments ON comments.id = blog.id */


namespace ODB\DB\Relation;

trait Main
{
	use Join;
	
}
