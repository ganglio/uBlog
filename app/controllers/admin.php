<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormError;

$admin=$app["controllers_factory"];

$admin->before($checkBefore);

$admin->get("/", function () use ($app) {
	return $app->redirect($app["url_generator"]->generate("dashboard"));
});

$admin->match("/login",function () use ($app) {
	$form = $app['form.factory']->createBuilder('form')
		->add('email', 'email', array(
			'label'  => 'Email',
			"label_attr" => array(
				"class" => "control-label",
			),
		))
		->add('password', 'password', array(
			'label'  => 'Password',
			"label_attr" => array(
				"class" => "control-label",
			),
		))->getForm();

	if ('POST' === $app["request"]->getMethod()) {
		$form->bindRequest($app['request']);

		if ($form->isValid()) {
			$email = $form->get("email")->getData();
			$password = $form->get("password")->getData();

			if ($app["users"]->authenticate($email,$password)) {
				$app["session"]->set("user", array(
					"email" => $email,
				));

				return $app->redirect($app["url_generator"]->generate("dashboard"));
			}

			$form->addError(new FormError('Wrong email or password.'));
		}
	}

	return $app["twig"]->render("admin.login.html.twig",array("form"=>$form->createView()));
})->bind("login");

$admin->match("/logout",function () use ($app) {
	$app["session"]->clear();

	if (NULL !== $app["request"]->get("fromhome"))
		return $app->redirect("/");
	else
		return $app->redirect("/admin");
})->bind("logout");

$app->mount("/admin/dashboard", include 'dashboard.php');
$app->mount("/admin/node", include 'nodes.php');


return $admin;