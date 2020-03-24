<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\ImagineFilters;

use Imagine\Image\ImageInterface;
use WebChemistry\ImageStorage\Filter\Filter;
use WebChemistry\ImageStorage\Scope\Scope;

interface FilterLoaderInterface
{

	public function addFilter(ImageFilterInterface $imageFilter): void;

	public function load(Filter $filter, Scope $scope, ImageInterface $image): void;

}
