<?php

namespace uBlog\Fields;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ValueType extends AbstractType
{
	public function getName() {
		return 'value';
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
	}

	public function buildForm(FormBuilderInterface $builder, array $options) {
	}

	public function buildView(FormView $view, FormInterface $form, array $options) {
	}
}