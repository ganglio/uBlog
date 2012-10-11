<?php

namespace uBlog\Fields;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MediaType extends AbstractType
{
	public function getName() {
		return 'media';
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'uBlog\Entity\Media',
		));
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
			->add("id","hidden")
			->add("type","choice",array(
				"required" => TRUE,
				"choices"=>array(
					"artwork" => "Artwork",
					"screenshot" => "Screenshot",
					"icon" => "Icon",
				),
				"empty_value" => "Select type",
				'constraints' => array(
					new Assert\Choice(array("artwork","screenshot","icon")),
					new Assert\NotBlank(array("message"=>"Please select a valid type")),
				),
			))
			->add("new_uri","file",array(
				"required" => FALSE,
				//'property_path' => FALSE,
			))
			->add("weight",'hidden',array(
				"empty_data"=>1000,
			))
			->add("uri",new ValueType())
		;
	}

	public function buildView(FormView $view, FormInterface $form, array $options) {}
}