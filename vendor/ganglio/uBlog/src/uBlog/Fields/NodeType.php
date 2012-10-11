<?php

namespace uBlog\Fields;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;
use uBlog\Fields\MediaType;

class NodeType extends AbstractType
{
	public function getName() {
		return 'node';
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add("language","choice",array(
				"label" => "Language",
				'choices' => array(
					"en" => "English",
					"fr" => "French",
					"it" => "Italian",
					"de" => "German",
					"es" => "Spanish",
				),
				"empty_value" => "Select language",
				'constraints' => array(
					new Assert\Choice(array("en","fr","it","de","es")),
					new Assert\NotBlank(array("message"=>"Please select a valid language")),
				),
			))
			->add('title', 'text', array(
				'label'  => 'Title',
				'constraints' => array(
					new Assert\NotBlank(),
					new Assert\MinLength(5)
				),
			))
			->add('content', 'textarea', array(
				'label'  => 'Body',
				'constraints' => array(
					new Assert\NotBlank(),
					new Assert\MinLength(5)
				),
				'attr' => array("rows" => 10,),
			))
			->add('status', 'choice', array(
				'label' => 'Status',
				'choices' => array(
					FALSE => "Unpublished",
					TRUE => "Published",
				),
				'constraints' => new Assert\Choice(array(FALSE, TRUE)),
			))
			->add("media","collection", array(
				"label" => "Media",
				"required" => "false",
				"type" => new MediaType(),
				"allow_add" => TRUE,
				"allow_delete" => TRUE,
				'by_reference' => FALSE,
			))
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options) {}
}