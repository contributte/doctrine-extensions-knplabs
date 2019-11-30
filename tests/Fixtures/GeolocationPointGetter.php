<?php declare(strict_types = 1);

namespace Tests\Nettrine\Extensions\KnpLabs\Fixtures;

use Knp\DoctrineBehaviors\ORM\Geocodable\Type\Point;

final class GeolocationPointGetter
{

	/**
	 * @return Point|false
	 */
	public function getPoint(object $entity)
	{
		return new Point(123, 456);
	}

}
