<?php declare(strict_types = 1);

namespace Nettrine\Extensions\KnpLabs\Security;

use Nette\Security\User;

final class UserCallable
{

	/** @var User */
	private $user;

	public function __construct(User $user)
	{
		$this->user = $user;
	}

	public function __invoke(): ?string
	{
		$id = $this->user->getId();

		return $id !== null ? (string) $id : null;
	}

}
