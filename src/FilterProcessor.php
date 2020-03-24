<?php declare(strict_types = 1);

namespace WebChemistry\ImageStorage\ImagineFilters;

use Imagine\Gd\Imagine as GdImagine;
use Imagine\Gmagick\Imagine as GmagickImagine;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Imagick\Imagine;
use RuntimeException;
use WebChemistry\ImageStorage\Exceptions\InvalidArgumentException;
use WebChemistry\ImageStorage\Filter\FilterProcessorInterface;
use WebChemistry\ImageStorage\Metadata\ImageMetadataInterface;
use WebChemistry\ImageStorage\Metadata\LocalImageSource;

final class FilterProcessor implements FilterProcessorInterface
{

	private ImagineInterface $imagine;

	private FilterLoaderInterface $loader;

	public function __construct(FilterLoaderInterface $loader, ?ImagineInterface $imagine = null)
	{
		$this->imagine = $imagine ?? $this->createImagine();
		$this->loader = $loader;
	}

	protected function createImagine(): ImagineInterface
	{
		if (extension_loaded('imagick')) {
			return new Imagine();
		}

		if (extension_loaded('gmagick')) {
			return new GmagickImagine();
		}

		if (extension_loaded('gd')) {
			return new GdImagine();
		}

		throw new RuntimeException('PHP extension not found, need imagick or gd or gmagick');
	}

	/**
	 * @param mixed[] $options
	 */
	public function process(ImageMetadataInterface $metadata, ?string $savePath = null, array $options = []): ?string
	{
		$resource = $metadata->getImage();
		$filter = $resource->getFilter();
		if (!$filter) {
			throw new InvalidArgumentException(sprintf('Image "%s" does not have filter', $resource->getId()));
		}

		$image = $this->createImageInstance($metadata);

		$this->loader->load($filter, $resource->getScope(), $image);

		if ($savePath) {
			$image->save($savePath);

			return null;
		}

		return $image->get($metadata->getMimeType()->toSuffix());
	}

	private function createImageInstance(ImageMetadataInterface $metadata): ImageInterface
	{
		$source = $metadata->getSource();
		if (!$source instanceof LocalImageSource) {
			throw new InvalidArgumentException(sprintf('Only %s source supported', LocalImageSource::class));
		}

		if ($path = $source->getPath()) {
			$image = $this->imagine->open($path);
		} elseif ($content = $source->getContent()) {
			$image = $this->imagine->load($content);
		} else {
			throw new InvalidArgumentException('Source of image metadata is invalid');
		}

		return $image;
	}

}
