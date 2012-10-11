<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Assert;
use uBlog\Fields\ValueType;
use uBlog\Fields\NodeType;
use uBlog\Repos\NodeRepository;
use uBlog\Entity\Node;

$nodes=$app["controllers_factory"];

$nodes->before($checkBefore);

$nodes
	->assert('id','([0-9]*)')
	->assert('locale',"(".implode("|",$app["all_locales"]).")")
;

$nodes->match("add",function () use ($app) {
	$node = new Node();

	$form = $app['form.factory']
		->createBuilder(new NodeType(), $node)
		->getForm()
	;

	if ('POST' === $app["request"]->getMethod()) {

		$form->bind($app['request']);
		if ($form->isValid()) {

			$app['db.orm.em']->persist($node);
			$app['db.orm.em']->flush();

			$app["session"]->setFlash("success","Content successfully created");

			return $app->redirect("/admin/node/edit/".$node->getId());
		}
	}

	return $app["twig"]->render("node.edit.html.twig",array(
		"user" => $app["session"]->get("user"),
		"title" => "Create content",
		"submit" => "Save",
		"form" => $form->createView(),
	));
});

$nodes->match("translate/{id}/{locale}", function($id, $locale) use ($app) {
	$node = $app["db.orm.em"]
		->getRepository("uBlog\Entity\Node")
		->find($id)
	;

	$translation = clone $node;
	$translation->setID(NULL);
	$translation->setLanguage($locale);
	$translation->setStatus(FALSE);
	$node->addTranslation($translation);

	print_r(count($translation->getMedia()));

	foreach ($translation->getMedia() as $media) {
		$new_media = clone $media;
		$translation->getMedia()->removeElement($media);
		$new_media->setID(NULL);
		$translation->addMedia($new_media);
	}

	print_r(count($translation->getMedia()));

	$form = $app['form.factory']
		->createBuilder(new NodeType(), $translation)
		->getForm()
	;

	if ('POST' === $app["request"]->getMethod()) {

		$form->bind($app['request']);

		if ($form->isValid()) {

			$app['db.orm.em']->persist($translation);
			$app['db.orm.em']->flush();

			$app["session"]->setFlash("success","Content successfully created");

			return $app->redirect("/admin/node/edit/".$translation->getId());
		}
	}

	return $app["twig"]->render("node.edit.html.twig",array(
		"user" => $app["session"]->get("user"),
		"title" => "Translate content",
		"submit" => "Save",
		"form" => $form->createView(),
	));
});

$nodes->match("edit/{id}", function ($id) use ($app) {

	$node = $app['db.orm.em']
		->getRepository("uBlog\Entity\Node")
		->find($id)
	;


	$form = $app['form.factory']
		->createBuilder(new NodeType(), $node)
		->add("removed","hidden",array(
			"property_path" => false,
		))
		->getForm()
	;

	if ('POST' === $app["request"]->getMethod()) {
		$form->bind($app['request']);

		if ($form->isValid()) {

			$removed_media = $app["request"]->get("node");
			$removed_media = $removed_media["removed"] ? explode(",",$removed_media["removed"]) : array();

			$media_repo = $app["db.orm.em"]
				->getRepository("uBlog\Entity\Media")
			;

			foreach ($removed_media as $media_id) {
				$media = $media_repo->find($media_id);
				$node->getMedia()->removeElement($media);
				$media->setNode(NULL);
				$app["db.orm.em"]->remove($media);
			}

			foreach ($node->getMedia() as $media) {
				$app['db.orm.em']->persist($media);
				$app['db.orm.em']->flush();
			}

			$app['db.orm.em']->persist($node);
			$app['db.orm.em']->flush();

			$app["session"]->setFlash("success","Content successfully saved");
			return $app->redirect("/admin/node/edit/$id");
		}
	}

	return $app["twig"]->render("node.edit.html.twig",array(
		"user" => $app["session"]->get("user"),
		"title" => "Edit content",
		"submit" => "Save",
		"form" => $form->createView(),
	));
});

$nodes->get("preview/{id}", function ($id) use ($app) {
	return "Preview $id";
});

$nodes->match("publish/{id}", function ($id) use ($app) {
	$old_status = $app["db.orm.em"]
		->createQuery("SELECT n.status FROM uBlog\Entity\Node n WHERE n.id = :id")
		->setParameters(array(
			"id" => $id,
		))
		->getSingleResult();

	if ('POST' === $app["request"]->getMethod()) {

		$res = $app["db.orm.em"]
			->createQuery("UPDATE uBlog\Entity\Node n SET n.status = 1-n.status WHERE n.id = :id")
			->setParameters(array(
				"id" => $id,
			))
			->execute()
		;

		$app["session"]->setFlash("notice","Content successfully ".($old_status["status"]?"unpublished":"published"));

		return $app->redirect("/admin");
	}

	return $app["twig"]->render("modals/alert.html.twig",array(
		"title" => ($old_status["status"]?"Unpublish":"Publish")." content",
		"submit" => ($old_status["status"]?"Unpublish":"Publish"),
		"action" => "/admin/node/publish/$id",
		"message" => "Are you sure?",
	));
});

$nodes->match("delete/{id}", function ($id) use ($app) {
	if ('POST' === $app["request"]->getMethod()) {

		$node = $app["db.orm.em"]
			->getRepository("uBlog\Entity\Node")
			->find($id)
		;

		$app["db.orm.em"]->remove($node);
		$app["db.orm.em"]->flush();

		$app["session"]->setFlash("notice","Content successfully deleted");

		return $app->redirect("/admin");
	}

	return $app["twig"]->render("modals/alert.html.twig",array(
		"title" => "Delete content",
		"submit" => "Delete",
		"action" => "/admin/node/delete/$id",
		"message" => "Are you sure?",
	));

});

$nodes->get("test/{id}", function ($id) use ($app) {
	$node = $app["db.orm.em"]
		->getRepository("uBlog\Entity\Node")
		->find($id)
	;
	foreach ($node->getTranslations() as $translation)
		echo $translation->getLanguage();
});


return $nodes;