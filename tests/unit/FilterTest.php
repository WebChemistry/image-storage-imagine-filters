<?php declare(strict_types = 1);

namespace Project\Tests;

use WebChemistry\ImageStorage\Entity\StorableImage;
use WebChemistry\ImageStorage\File\FileFactory;
use WebChemistry\ImageStorage\Filesystem\LocalFilesystem;
use WebChemistry\ImageStorage\ImagineFilters\FilterProcessor;
use WebChemistry\ImageStorage\ImagineFilters\OperationRegistry;
use WebChemistry\ImageStorage\ImagineFilters\Testing\FileTestCase;
use WebChemistry\ImageStorage\ImagineFilters\Testing\Filter\ThumbnailOperation;
use WebChemistry\ImageStorage\PathInfo\Factory\PathInfoFactory;
use WebChemistry\ImageStorage\Uploader\FilePathUploader;

class FilterTest extends FileTestCase
{

	private FilterProcessor $processor;

	protected function _before(): void
	{
		parent::_before();

		$registry = new OperationRegistry();
		$registry->add(new ThumbnailOperation());
		$this->processor = new FilterProcessor($registry);
	}

	public function testFilter(): void
	{
		$image = new StorableImage(
			new FilePathUploader($this->imageJpg),
			'name.jpg'
		);
		$image = $image->withFilter('thumbnail');

		$fileFactory = new FileFactory(new LocalFilesystem($this->getAbsolutePath()), new PathInfoFactory());

		$content = $this->processor->process(
			$fileFactory->create($image),
			$fileFactory->create($image->getOriginal())
		);

		$size = getimagesizefromstring($content);
		$this->assertSame(15, $size[0], 'width is not same');
		$this->assertSame(15, $size[1], 'height is not same');
	}

}
