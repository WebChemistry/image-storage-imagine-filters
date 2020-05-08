<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\ImagineFilters;

use Imagine\Image\ImageInterface;
use WebChemistry\ImageStorage\Filter\FilterInterface;
use WebChemistry\ImageStorage\Scope\Scope;

interface OperationInterface
{

	public function supports(FilterInterface $filter, Scope $scope): bool;

	public function operate(ImageInterface $image, FilterInterface $filter): void;

}
