<?php
define('_JEXEC', 1);
define('_API', 1);
define('JPATH_BASE', dirname(dirname(dirname(__FILE__))));

// Include the Joomla framework
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';

$application = JFactory::getApplication('site');
$application->initialise();

require '../Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
	'mode' => 'development'
));

$app->_db    = JFactory::getDbo();
$app->_input = JFactory::getApplication()->input;

$app->view(new \JsonApiView());
$app->add(new \JsonApiMiddleware());

// Main entry
$app->get('/', function () use ($app)
	{

		$user = JFactory::getUser();
		$name = !$user->guest ? $user->name : 'guest';

		$app->render(
			200, array(
				'msg' => 'Welcome' . ' ' . $name,
			)
		);
	}
);

// Content
$app->map('/content/', function () use ($app)
	{
		$query = $app->_db->getQuery(true);
		$query->select('*')
			->from($app->_db->quoteName('#__content'))
			->where($app->_db->quoteName('state') . ' = ' . $app->_db->quote('1'));
		$app->_db->setQuery($query);

		$app->render(200, array(
				'msg' => $app->_db->loadObjectList(),
			)
		);
	}
)->via('GET');

$app->map('/content/:id', function ($id) use ($app)
	{
		$query = $app->_db->getQuery(true);
		$query->select('*')
			->from($app->_db->quoteName('#__content'))
			->where('id = ' . $app->_db->quote($id)
				. ' AND ' . $app->_db->quoteName('state') . ' = ' . $app->_db->quote('1')
			);
		$app->_db->setQuery($query);

		$app->render(200, array(
				'msg' => $app->_db->loadObject(),
			)
		);
	}
)->via('GET');

$app->map('/content/', function () use ($app)
	{
		$user = JFactory::getUser();
		if (count($user->getAuthorisedCategories('com_content', 'core.create')) > 0)
		{
			$row            = new stdClass();
			$row->title     = $app->_input->get('title') . rand();
			$row->introtext = $app->_input->get('introtext') . rand();
			$row->state     = '1';

			$app->_db->insertObject('#__content', $row);

			$app->render(200, array(
					'msg' => $row->title . ' created!',
				)
			);
		}

		$app->render(403, array(
				'msg' => 'Not authorized',
			)
		);

	}
)->via('POST');

$app->map('/content/:id', function ($id) use ($app)
	{

		$user = JFactory::getUser();
		if ($user->authorise('core.edit', 'com_content.article.' . $id) || $user->authorise('core.edit.own', 'com_content.article.' . $id))
		{
			$row            = new stdClass();
			$row->id        = $id;
			$row->title     = $app->_input->get('title');
			$row->introtext = $app->_input->get('introtext');
			$row->state     = '1';

			$result = $app->_db->updateObject('#__content', $row, 'id');

			$app->render(200, array(
					'msg' => $result,
				)
			);
		}

		$app->render(403, array(
				'msg' => 'Not authorized',
			)
		);
	}
)->via('PUT');

$app->map('/content/:id', function ($id) use ($app)
	{
		$query = $app->_db->getQuery(true);
		$query->delete($app->_db->quoteName('#__content'))
			->where('id = ' . $app->_db->quote($id));
		$app->_db->setQuery($query);

		$app->render(200, array(
				'msg' => $app->_db->query(),
			)
		);
	}
)->via('DELETE');

$app->run();
