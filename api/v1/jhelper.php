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
	}

	public function checkUserSession()
	{
		$this->session->getId();
	}
}
