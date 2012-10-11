<?php

use Symfony\Component\HttpFoundation\Response;
use uBlog\SEControllers\UsersProvider;
use uBlog\Entity\Node;

$dashboard=$app["controllers_factory"];

$dashboard->before($checkBefore);

$dashboard->get("/",function () use ($app) {

	return $app["twig"]->render("admin.home.html.twig",array(
		"user"=>$app["session"]->get("user"),
		"query"=>$app["request"]->getQueryString(),
	));
})->bind("dashboard");

return $dashboard;