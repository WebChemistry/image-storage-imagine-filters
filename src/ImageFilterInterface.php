<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\ImagineFilters;

use Imagine\Image\ImageInterface;
use WebChemistry\ImageStorage\Filter\Filter;
use WebChemistry\ImageStorage\Scope\Scope;

interface ImageFilterInterface
{

	public function supports(Filter $filter, Scope $scope): bool;

	public function filter(ImageInterface $image, Filter $filter): void;

}
