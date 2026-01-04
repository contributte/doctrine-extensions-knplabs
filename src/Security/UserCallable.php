<?php declare(strict_types = 1);

namespace Nettrine\Extensions\KnpLabs\Security;

use Knp\DoctrineBehaviors\Contract\Provider\UserProviderInterface;
use Nette\Security\User;

final class UserCallable implements UserProviderInterface
{

	public function __construct(
		private User $user,
	)
	{
	}

	public function provideUser(): ?string
	{
		$id = $this->user->getId();

		return $id !== null ? (string) $id : null;
	}

	public function provideUserEntity(): string
	{
		return $this->user::class;
	}

}
