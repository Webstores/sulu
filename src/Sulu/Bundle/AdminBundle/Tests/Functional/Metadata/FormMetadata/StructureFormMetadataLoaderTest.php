<?php

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Bundle\AdminBundle\Tests\Functional\Metadata\FormMetadata;

use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\FormMetadata;
use Sulu\Bundle\AdminBundle\Metadata\FormMetadata\TypedFormMetadata;
use Sulu\Bundle\AdminBundle\Metadata\SchemaMetadata\SchemaMetadata;
use Sulu\Bundle\TestBundle\Testing\KernelTestCase;

class StructureFormMetadataLoaderTest extends KernelTestCase
{
    /**
     * @var StructureFormMetadataLoader
     */
    private $structureFormMetadataLoader;

    public function setUp()
    {
        $this->structureFormMetadataLoader = $this->getContainer()->get('sulu_admin_test.structure_form_metadata_loader');
    }

    public function testGetMetadataEnglish()
    {
        $typedForm = $this->structureFormMetadataLoader->getMetadata('page', 'en');
        $this->assertInstanceOf(TypedFormMetadata::class, $typedForm);
        $this->assertCount(2, $typedForm->getForms());

        $overviewForm = $typedForm->getForms()['overview'];
        $this->assertInstanceOf(FormMetadata::class, $overviewForm);
        $this->assertEquals('overview', $overviewForm->getName());
        $this->assertEquals('Overview', $overviewForm->getTitle());
        $this->assertCount(6, $overviewForm->getItems());
        $this->assertNotNull($overviewForm->getSchema());
        $this->assertInstanceOf(SchemaMetadata::class, $overviewForm->getSchema());

        $defaultForm = $typedForm->getForms()['default'];
        $this->assertInstanceOf(FormMetadata::class, $defaultForm);
        $this->assertEquals('default', $defaultForm->getName());
        $this->assertEquals('Animals', $defaultForm->getTitle());
        $this->assertCount(5, $defaultForm->getItems());
        $this->assertNotNull($defaultForm->getSchema());
        $this->assertInstanceOf(SchemaMetadata::class, $defaultForm->getSchema());
    }

    public function testGetMetadataGerman()
    {
        $typedForm = $this->structureFormMetadataLoader->getMetadata('page', 'de');
        $this->assertInstanceOf(TypedFormMetadata::class, $typedForm);
        $this->assertCount(2, $typedForm->getForms());

        $overviewForm = $typedForm->getForms()['overview'];
        $this->assertInstanceOf(FormMetadata::class, $overviewForm);
        $this->assertEquals('overview', $overviewForm->getName());
        $this->assertEquals('Overview', $overviewForm->getTitle());
        $this->assertCount(6, $overviewForm->getItems());
        $this->assertNotNull($overviewForm->getSchema());
        $this->assertInstanceOf(SchemaMetadata::class, $overviewForm->getSchema());

        $defaultForm = $typedForm->getForms()['default'];
        $this->assertInstanceOf(FormMetadata::class, $defaultForm);
        $this->assertEquals('default', $defaultForm->getName());
        $this->assertEquals('Tiers', $defaultForm->getTitle());
        $this->assertCount(5, $defaultForm->getItems());
        $this->assertNotNull($defaultForm->getSchema());
        $this->assertInstanceOf(SchemaMetadata::class, $defaultForm->getSchema());
    }

    public function testGetMetadataWhichDoesNotExist()
    {
        $typedForm = $this->structureFormMetadataLoader->getMetadata('does_not_exist', 'en');
        $this->assertNull($typedForm);
        $typedForm = $this->structureFormMetadataLoader->getMetadata('does_not_exist', 'de');
        $this->assertNull($typedForm);
    }
}
