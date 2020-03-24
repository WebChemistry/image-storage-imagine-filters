<?php declare(strict_types = 1);

namespace Project\Tests;

use WebChemistry\ImageStorage\Entity\Image;
use WebChemistry\ImageStorage\ImagineFilters\FilterLoader;
use WebChemistry\ImageStorage\ImagineFilters\FilterProcessor;
use WebChemistry\ImageStorage\ImagineFilters\Testing\FileTestCase;
use WebChemistry\ImageStorage\ImagineFilters\Testing\Filter\ThumbnailFilter;
use WebChemistry\ImageStorage\Metadata\ImageMetadata;
use WebChemistry\ImageStorage\Metadata\LocalImageSource;

class FilterTest extends FileTestCase
{

	private FilterProcessor $processor;

	protected function _before(): void
	{
		parent::_before();

		$loader = new FilterLoader();
		$loader->addFilter(new ThumbnailFilter());
		$this->processor = new FilterProcessor($loader);
	}

	public function testFilterWithSave(): void
	{
		$image = new class('name.jpg') extends Image {

		};
		$image = $image->withFilter('thumbnail');

		$this->processor->process(
			new ImageMetadata($image, new LocalImageSource($this->imageJpg)),
			$this->getAbsolutePath('name.jpg')
		);

		$this->assertTempFileExists('name.jpg');
		$size = getimagesize($this->getAbsolutePath('name.jpg'));
		$this->assertSame(15, $size[0], 'width is not same');
		$this->assertSame(15, $size[1], 'height is not same');
	}

	public function testFilterWithString(): void
	{
		$image = new class('name.jpg') extends Image {

		};
		$image = $image->withFilter('thumbnail');

		$this->processor->process(
			new ImageMetadata($image, new LocalImageSource($this->imageJpg)),
			$this->getAbsolutePath('name2.jpg')
		);

		$this->assertTempFileExists('name2.jpg');

		$string = $this->processor->process(new ImageMetadata($image, new LocalImageSource($this->imageJpg)));

		$this->assertSame($string, file_get_contents($this->getAbsolutePath('name2.jpg')));
	}

}
