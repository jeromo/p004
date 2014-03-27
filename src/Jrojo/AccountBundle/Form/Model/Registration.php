<?php
// src/Jrojo/AccountBundle/Form/Model/Registration.php
namespace Jrojo\AccountBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Jrojo\AccountBundle\Entity\User;
class Registration
{
	/**
	 * @Assert\Type(type="Jrojo\AccountBundle\Entity\User")
	 * @Assert\Valid()
	 */
	protected $user;
	/**
	 * @Assert\NotBlank()
	 * @Assert\True()
	 */
	protected $termsAccepted;
	public function setUser(User $user)
	{
		$this->user = $user;
	}
	public function getUser()
	{
		return $this->user;
	}
	public function getTermsAccepted()
	{
		return $this->termsAccepted;
	}
	public function setTermsAccepted($termsAccepted)
	{
		$this->termsAccepted = (Boolean) $termsAccepted;
	}
}