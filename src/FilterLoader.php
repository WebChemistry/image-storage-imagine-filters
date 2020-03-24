<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\ImagineFilters;

use Imagine\Image\ImageInterface;
use WebChemistry\ImageStorage\Filter\Filter;
use WebChemistry\ImageStorage\ImagineFilters\Exceptions\FilterNotFoundException;
use WebChemistry\ImageStorage\Scope\Scope;

final class FilterLoader implements FilterLoaderInterface
{

	/** @var ImageFilterInterface[] */
	private array $filters = [];

	public function addFilter(ImageFilterInterface $imageFilter): void
	{
		$this->filters[] = $imageFilter;
	}

	public function load(Filter $filter, Scope $scope, ImageInterface $image): void
	{
		foreach ($this->filters as $loader) {
			if ($loader->supports($filter, $scope)) {
				$loader->filter($image, $filter);

				return;
			}
		}

		throw new FilterNotFoundException(sprintf('Filter loader for filter "%s" not found', $filter->getName()));
	}

}
