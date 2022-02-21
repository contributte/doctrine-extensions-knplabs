<?php declare(strict_types = 1);

namespace Nettrine\Extensions\KnpLabs\Security;

use Nette\Security\User;

final class UserCallable implements \Knp\DoctrineBehaviors\Contract\Provider\UserProviderInterface
{

	/** @var User */
	private $user;

	public function __construct(User $user)
	{
		$this->user = $user;
	}


	public function provideUser()
	{
		$id = $this->user->getId();

		return $id !== null ? (string) $id : null;
	}


	public function provideUserEntity(): ?string
	{
		return \get_class($this->user);
	}

}
