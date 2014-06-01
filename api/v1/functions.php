<?php

/**
 * @version     api/v1/functions.php 2014-22-05 14:21:00Z pav
 * @package     Watchful Master
 * @author      Watchfuls
 * @copyright   (c) 2012-2013, Watchful
 */
function authentificate()
{
    $app = \Slim\Slim::getInstance();
    $app->environment['PATH_INFO'] = strtolower($app->environment['PATH_INFO']);
    $user = \JFactory::getUser();

    if ($user->guest)
    {

        if ($app->request->headers->get('api_key'))
        {
            $api_key = $app->request->headers->get('api_key');
        } elseif ($app->request->get('api_key'))
        {
            $api_key = $app->request->get('api_key');
        } else
        {
            throw new Exception('Not API Key', 403);
        }

        $app->_db->setQuery("SELECT * FROM thu_api_keys WHERE hash = '" . $api_key . "'");
        $token = $app->_db->loadObject();

        if (!$token or !$token->published)
        {
            throw new Exception('Not authorize Key', 404);
        } else
        {
            //login the user for Joomla classes
            $user = JFactory::getUser($token->user_id);
            $session = & JFactory::getSession();
            $session->set('user', $user);

            //easiest way to have the current user everywhere
            $app->user = $user;
            $app->dataDogsTags->userid = $token->user_id;
        }
    } else
    {
        $app->user = $user;
        $app->dataDogsTags->userid = $user->id;
    }
}
