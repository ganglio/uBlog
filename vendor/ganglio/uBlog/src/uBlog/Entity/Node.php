<?php

namespace uBlog\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="uBlog\Repos\NodeRepository")
 * @Table(name="node")
 **/
class Node {

	/** @Id @Column(type="integer") @GeneratedValue **/
	protected $id;
	/** @Column(type="string") **/
	protected $title;
	/** @Column(type="text") **/
	protected $content;
	/** @Column(type="boolean") **/
	protected $status;
	/** @Column(type="datetime") **/
	protected $created;
	/** @Column(type="string") **/
	protected $slug;
	/** @Column(type="string") **/
	protected $language;
	/** @Column(type="integer") **/
	protected $weight;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @OneToMany(targetEntity="Node", mappedBy="parent",cascade={"persist","remove"}, fetch="EAGER")
	 * @OrderBy({"weight" = "ASC"})
	 * @JoinColumn(name="parent_id", referencedColumnName="id", nullable=true)
	 */
	protected $translations = NULL;

	/**
	 * @ManyToOne(targetEntity="Node", inversedBy="translations")
	 **/
	protected $parent = NULL;

	/**
	 * @var \Doctrine\Common\Collections\ArrayCollection
	 * @OneToMany(targetEntity="Media", mappedBy="node",cascade={"persist","remove"})
	 * @JoinColumn(name="node_id", referencedColumnName="id", nullable=true)
	 */
	protected $media = NULL;

	public function __construct() {
		$this->media = new ArrayCollection();
		$this->translations = new ArrayCollection();
		$this->weight = 1000;
	}

	/// Media
	/**
	 * setMedia
	 * @param ArrayCollection $medias
	 */
	public function setMedia($medias) {
		foreach ($medias as $media)
			$this->addMedia($media);
	}
	public function addMedia(Media $new_media) {
		$is_there = FALSE;
		foreach ($this->media as $media) {
			if ($media->getID() === $new_media->getID())
				$is_there = TRUE;
		}

		if (!$is_there || $new_media->getID() == NULL) {
			$this->media->add($new_media);
			$new_media->setNode($this);
		}
	}
	public function getMedia() {
		return $this->media;
	}

	// Translations
	/**
	 * setTranslations
	 * @param  ArrayCollection $tranlsations
	 */
	public function setTranslations($translations) {
		$this->translations = $translations;
	}
	public function addTranslation(Node $translation) {
		$this->translations->add($translation);
		$translation->setParent($this);
	}
	public function getTranslations() {
		return $this->translations;
	}
	public function enumTranslations() {
		$out=array($this->language=>$this->id);
		foreach ($this->translations as $translation)
			$out[$translation->getLanguage()]=$translation->getID();
		return $out;
	}

	// Parent
	public function setParent($parent) {
		$this->parent = $parent;
	}
	public function getParent() {
		return $this->parent;
	}

	/// Content
	public function getContent() {
		return $this->content;
	}
	public function setContent($content) {
		$this->content = $content;
	}

	/// Title
	public function getTitle() {
		return $this->title;
	}
	public function setTitle($title) {
		$this->title = $title;
		$this->setSlug($title);
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
		return $this->status;
	}
	public function setStatus($status) {
		$this->status = $status;
	}
	public function getClassStatus() {
		return $this->status ? "published" : "unpublished";
	}

	/// slug
	public function setSlug($slug) {
		setlocale(LC_ALL, 'en_GB');
		$slugified = mb_convert_case($slug, MB_CASE_LOWER);
		$slugified = mb_convert_encoding($slugified, 'ASCII');

		$slugified = preg_replace('~[^\\pL\d]+~u', '-', $slugified);
		$slugified = trim($slugified, '-');

		$slugified = preg_replace('~[^-\w]+~', '', $slugified);
		if (empty($slugified))
			return 'n-a';

		$this->slug = $slugified;
	}
	public function getSlug() {
		return $this->slug;
	}

	/// language
	public function setLanguage($language) {
		$this->language = $language;
	}
	public function getLanguage() {
		return $this->language;
	}

	/// weight
	public function setWeight($weight) {
		$this->weight = $weight;
	}
	public function getWeight() {
		return $this->weight;
	}

	// toArray
	public function toArray() {
		foreach ($this as $kk=>$vv)
			if ("media" !== $kk)
				$out[$kk] = $vv;
		unset($out["translations"]);
		foreach ($this->media as $media)
			if (!isset($out["media"][$media->getType()]))
				$out["media"][$media->getType()]=$media->getURI();
			else if (!is_array($out["media"][$media->getType()])) {
				$tmp = $out["media"][$media->getType()];
				$out["media"][$media->getType()] = array(
					$tmp,
					$media->getURI(),
				);
			} else
				$out["media"][$media->getType()][] = $media->getURI();
		return $out;
	}
}

?>