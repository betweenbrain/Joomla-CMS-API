<?php
define('_JEXEC', 1);
define('_API', 1);

define('JPATH_BASE', dirname(dirname(dirname(__FILE__))));

// Include the Joomla framework
require_once(JPATH_BASE . '/includes/defines.php');
require_once(JPATH_BASE . '/includes/framework.php');

// Include Joomla helper class
require_once(dirname(__FILE__) . '/jhelper.php');
$helper = new jhelper;

require '../Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
	'mode' => 'development'
));

$db    = JFactory::getDbo();
$input = JFactory::getApplication()->input;

$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());

// Main entry
$app->get('/', function () use ($app)
	{
		$app->render(
			200, array(
				'msg' => 'You reach the JAB API V1',
			)
		);
	}
);

// Content
$app->map('/content/', function () use ($app, $db)
	{
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__content'))
			->where($db->quoteName('state') . ' = ' . $db->quote('1'));
		$db->setQuery($query);

		$app->render(200, array(
				'msg' => $db->loadObjectList(),
			)
		);
	}
)->via('GET');

$app->map('/content/:id', function ($id) use ($app, $db)
	{
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__content'))
			->where('id = ' . $db->quote($id)
				. ' AND ' . $db->quoteName('state') . ' = ' . $db->quote('1')
			);
		$db->setQuery($query);

		$app->render(200, array(
				'msg' => $db->loadObject(),
			)
		);
	}
)->via('GET');

$app->map('/content/', function () use ($app, $db, $input)
	{
		$row            = new stdClass();
		$row->title     = $input->get('title');
		$row->introtext = $input->get('introtext');
		$row->state     = '1';

		$result = $db->insertObject('#__content', $row);

		$app->render(200, array(
				'msg' => $result,
			)
		);
	}
)->via('POST');

$app->map('/content/:id', function ($id) use ($app, $db, $input)
	{
		$row            = new stdClass();
		$row->id        = $id;
		$row->title     = $input->get('title');
		$row->introtext = $input->get('introtext');
		$row->state     = '1';

		$result = $db->updateObject('#__content', $row, 'id');

		$app->render(200, array(
				'msg' => $result,
			)
		);
	}
)->via('PUT');

$app->map('/content/:id', function ($id) use ($app, $db)
	{
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__content'))
			->where('id = ' . $db->quote($id));
		$db->setQuery($query);

		$app->render(200, array(
				'msg' => $db->query(),
			)
		);
	}
)->via('DELETE');

$app->run();
