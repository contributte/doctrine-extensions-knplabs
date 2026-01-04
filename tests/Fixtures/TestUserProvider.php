<?php declare(strict_types = 1);

namespace Tests\Fixtures;

use Knp\DoctrineBehaviors\Contract\Provider\UserProviderInterface;

final class TestUserProvider implements UserProviderInterface
{

	public function provideUser(): ?string
	{
		return 'test-user';
	}

	public function provideUserEntity(): ?string
	{
		return null;
	}

}
