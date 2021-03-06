<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\ImagineFilters;

use Imagine\Gd\Imagine as GdImagine;
use Imagine\Gmagick\Imagine as GmagickImagine;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Imagick\Imagine;
use RuntimeException;
use WebChemistry\ImageStorage\File\FileInterface;
use WebChemistry\ImageStorage\Filter\FilterProcessorInterface;
use WebChemistry\ImageStorage\ImagineFilters\Exceptions\OperationNotFoundException;

final class FilterProcessor implements FilterProcessorInterface
{

	private ImagineInterface $imagine;

	private OperationRegistryInterface $operationRegistry;

	public function __construct(OperationRegistryInterface $operationRegistry, ?ImagineInterface $imagine = null)
	{
		$this->imagine = $imagine ?? $this->createImagine();
		$this->operationRegistry = $operationRegistry;
	}

	protected function createImagine(): ImagineInterface
	{
		if (extension_loaded('imagick')) {
			return new Imagine();
		}

		if (extension_loaded('gd')) {
			return new GdImagine();
		}

		if (extension_loaded('gmagick')) {
			return new GmagickImagine();
		}

		throw new RuntimeException('PHP extension not found, need imagick or gd or gmagick');
	}

	/**
	 * @param mixed[] $options
	 */
	public function process(FileInterface $target, FileInterface $source, array $options = []): string
	{
		$filter = $target->getImage()->getFilter();
		if (!$filter) {
			return $target->getContent();
		}

		$operation = $this->operationRegistry->get($filter, $target->getImage()->getScope());

		if (!$operation) {
			throw new OperationNotFoundException(sprintf('Operation not found for %s', $target->getImage()->getId()));
		}

		$operation->operate($image = $this->createImageInstance($source), $filter);

		return $image->get($source->getMimeType()->toSuffix());
	}

	private function createImageInstance(FileInterface $file): ImageInterface
	{
		return $this->imagine->load($file->getContent());
	}

}
