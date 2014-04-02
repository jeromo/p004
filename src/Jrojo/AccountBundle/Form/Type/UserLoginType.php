<?php
// src/Jrojo/AccountBundle/Form/Type/UserLoginType.php
namespace Jrojo\AccountBundle\Form\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
class UserLoginType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('email', 'email');
		$builder->add('password', 'password');
		$builder->add('save', 'submit');		
	}
	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
		$resolver->setDefaults(array(
				'data_class' => 'Jrojo\AccountBundle\Entity\User'
		));
	}
	public function getName()
	{
		return 'user';
	}
}