<?php

use Symfony\Component\HttpFoundation\Response;

$widgets=$app["controllers_factory"];

$widgets->before($checkBefore);

$widgets->get("/latest",function () use ($app) {
	$nodes_per_page = 50;

	$page = $app["request"]->get("p");

	$tnodes = $app['db.orm.em']
		->createQuery('SELECT COUNT(n) as cnt FROM uBlog\Entity\Node n WHERE n.parent IS NULL')
		->getSingleResult()
	;

	$total_pages=ceil($tnodes["cnt"]/$nodes_per_page)-1;

	$nodes = $app["db.orm.em"]
		->getRepository("uBlog\Entity\Node")
		->findBy(array("parent"=>NULL),array("weight"=>"ASC"),$nodes_per_page, $page * $nodes_per_page)
	;

	$node = array();
	foreach ($nodes as $data) {
		$translations = array();
		foreach ($data->getTranslations() as $translation)
			$translations[$translation->getLanguage()] = array(
				"id" => $translation->getId(),
				"media_count" => count($translation->getMedia()),
				"status" => $translation->getClassStatus(),
			);

		$node[]=array(
			"id" => $data->getId(),
			"weight" => $data->getWeight(),
			"title"=> $data->getTitle(),
			"created" => $data->getCreated(),
			"translations" => $translations,
			"languages" => $data->enumTranslations(),
			"language" => $data->getLanguage(),
			"media_count" => count($data->getMedia()),
			"status" => $data->getClassStatus(),
		);
	}

	return $app["twig"]->render("widgets/latest.html.twig",array(
		"id" => "latest",
		"title" => "Content",
		"span" => 12,
		"nodes" => $node,
		"current_page" => empty($page)?0:$page,
		"total_pages" => $total_pages,
	));
});

$widgets->post("/latest/fixweight",function() use ($app) {
	$query = $app["db.orm.em"]->createQuery("UPDATE uBlog\Entity\Node n SET n.weight = :weight WHERE n.id = :id");

	foreach ($app["request"]->get("weight") as $id=>$weight) {
		echo "$id $weight - ";
		$res = $query
			->setParameters(array(
				"id" => $id,
				"weight" => $weight,
			))->execute()
		;
		var_dump($res);
	}
});

return $widgets;