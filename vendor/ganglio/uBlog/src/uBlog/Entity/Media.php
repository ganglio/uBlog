<?php

namespace uBlog\Entity;

/**
 * @Entity @Table(name="media")
 **/
class Media {

	/** @Id @Column(type="integer") @GeneratedValue **/
	protected $id;
	/** @Column(type="string") **/
	protected $type;
	/** @Column(type="string") **/
	protected $uri;
	/** @Column(type="boolean") **/
	protected $published;
	/** @Column(type="datetime") **/
	protected $created;
	/** @Column(type="integer") **/
	protected $weight;

	/**
	 * @ManyToOne(targetEntity="Node", inversedBy="media")
	 **/
	protected $node;

	public function __construct() {
		$this->published = TRUE;
		$this->created = new \DateTime();
		$this->weight = 1000;
	}

	/// Node
	public function setNode($node) {
		$this->node = $node;
	}
	public function getNode() {
		return $this->node;
	}

	/// Type
	public function getType() {
		return $this->type;
	}
	public function setType($type) {
		$this->type = $type;
	}

	/// URI
	public function getURI() {
		return $this->uri;
	}
	public function setURI($uri) {
	}
	// NewUri helpers
	public function getNewUri() {
		return $this->uri;
	}
	/// Update URI
	public function setNewUri($new_uri) {
		if ("Symfony\Component\HttpFoundation\File\UploadedFile" === get_class($new_uri)) {
			$fname = md5(mt_rand().time()).".".$new_uri->getClientOriginalName();
			$new_uri->move("public/".$this->type,$fname);
			$this->uri = "/public/".$this->type."/$fname";
		}
	}

	/// Created
	public function getCreated() {
		return $this->created->format("d-m-Y H:i");
	}

	/// ID
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id=$id;
	}

	/// Status
	public function getStatus() {
		return $this->published;
	}
	public function setStatus($status) {
		$this->published = $status;
	}

	/// weight
	public function setWeight($weight) {
		$this->weight = $weight;
	}
	public function getWeight() {
		return $this->weight;
	}
}

?>