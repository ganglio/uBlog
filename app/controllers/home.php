<?php

use Symfony\Component\HttpFoundation\Response;

$home=$app["controllers_factory"];

$home->assert('locale',"(".implode("|",$app["all_locales"]).")");

$home->get('/', function () use ($app) {
	$default_locale = getAcceptLanguage();
	return $app->redirect("/".$default_locale);
});

$home->get('/{locale}/', function ($locale) use ($app) {
	$app["locale"] = $locale;

	$nodes = $app["db.orm.em"]
		->getRepository('uBlog\Entity\Node')
		->findWhere(array("status"=>1,"language"=>$locale));

	return $app['twig']->render('home.html.twig',
		array(
			'locale' => $locale,
			'user' => $app["session"]->get("user"),
			'all_locales' => $app['all_locales'],
			'nodes' => $nodes,
		));
});

function getAcceptLanguage() {
	$AL = explode(",",$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
	return substr($AL[0],0,2);
}

return $home;