<?php
// repositories/BugRepository.php

namespace uBlog\Repos;

use Doctrine\ORM\EntityRepository;

class NodeRepository extends EntityRepository {
	public function find($id) {
		$dql="SELECT n,m FROM uBlog\Entity\Node n LEFT JOIN n.media m WHERE n.id = :id ORDER BY m.weight ASC";

		$res = $this
			->getEntityManager()
			->createQuery($dql)
			->setParameters(array(
				"id" => $id,
			))
			->getResult()
		;
		return $res[0];
	}

	public function findWhere($params = array()) {
		$dql="SELECT n,m FROM uBlog\Entity\Node n LEFT JOIN n.media m WHERE n.status = :status AND n.language = :language ORDER BY m.weight ASC";

		$res = $this
			->getEntityManager()
			->createQuery($dql)
			->setParameters($params)
			->getResult()
		;
		return $res;
	}
}
