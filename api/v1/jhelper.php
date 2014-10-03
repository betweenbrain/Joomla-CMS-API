<?php defined('_JEXEC') or die;

/**
 * File       jhelper.php
 * Created    10/3/14 3:00 PM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2014 betweenbrain llc. All Rights Reserved.
 * License    GNU GPL v2 or later
 */
class jhelper
{

	public function __construct()
	{
		$this->session = JFactory::getSession();
		$this->app     = JFactory::getApplication('site');
		$this->app->initialise();
		$this->db = JFactory::getDbo();
	}

	public function checkUserSession()
	{
		$this->session->getId();
	}

	public function isUser()
	{
		$query = $this->db->getQuery(true);
		$query->select('userid');
		$query->from($this->db->quoteName('#__session'));
		$query->where($this->db->quoteName('session_id') . " = " . $this->db->quote($this->checkUserSession()));

		$this->db->setQuery($query);

		return $this->checkUserSession();

		return ($this->db->loadResult() === 0) ? true: false;
	}
}
