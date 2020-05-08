<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\ImagineFilters\Testing\Filter;

use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use WebChemistry\ImageStorage\Filter\FilterInterface;
use WebChemistry\ImageStorage\ImagineFilters\OperationInterface;
use WebChemistry\ImageStorage\Scope\Scope;

final class ThumbnailOperation implements OperationInterface
{

	public function supports(FilterInterface $filter, Scope $scope): bool
	{
		return $filter->getName() === 'thumbnail';
	}

	public function operate(ImageInterface $image, FilterInterface $filter): void
	{
		$image->resize(new Box(15, 15));
	}

}
