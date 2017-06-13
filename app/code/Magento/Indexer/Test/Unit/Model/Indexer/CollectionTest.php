<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Indexer\Test\Unit\Model\Indexer;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadData()
    {
        $indexerIdOne = 'first_indexer_id';
        $indexerIdSecond = 'second_indexer_id';

        $entityFactory = $this->getMockBuilder(
            \Magento\Framework\Data\Collection\EntityFactoryInterface::class
        )->disableOriginalConstructor()->setMethods(
            ['create']
        )->getMock();

        $config = $this->getMockBuilder(\Magento\Framework\Indexer\ConfigInterface::class)->getMock();

        $statesFactory = $this->getMockBuilder(
            \Magento\Indexer\Model\ResourceModel\Indexer\State\CollectionFactory::class
        )->disableOriginalConstructor()->setMethods(
            ['create']
        )->getMock();

        $states = $this->getMockBuilder(
            \Magento\Indexer\Model\ResourceModel\Indexer\State\Collection::class
        )->disableOriginalConstructor()->getMock();

        $state = $this->getMockBuilder(
            \Magento\Indexer\Model\Indexer\State::class
        )->setMethods(
            ['getIndexerId', '__wakeup']
        )->disableOriginalConstructor()->getMock();

        $state->expects($this->any())->method('getIndexerId')->will($this->returnValue('second_indexer_id'));

        $indexer = $this->getMockBuilder(
            \Magento\Indexer\Model\Indexer\Collection::class
        )->setMethods(
            ['load', 'setState']
        )->disableOriginalConstructor()->getMock();

        $indexer->expects($this->once())->method('setState')->with($state);

        $indexer->expects($this->any())->method('load')->with($this->logicalOr($indexerIdOne, $indexerIdSecond));

        $entityFactory->expects(
            $this->any()
        )->method(
            'create'
        )->with(
            \Magento\Framework\Indexer\IndexerInterface::class
        )->will(
            $this->returnValue($indexer)
        );

        $statesFactory->expects($this->once())->method('create')->will($this->returnValue($states));

        $config->expects(
            $this->once()
        )->method(
            'getIndexers'
        )->will(
            $this->returnValue([$indexerIdOne => 1, $indexerIdSecond => 2])
        );

        $states->expects($this->any())->method('getItems')->will($this->returnValue([$state]));

        $collection = new \Magento\Indexer\Model\Indexer\Collection($entityFactory, $config, $statesFactory);
        $this->assertInstanceOf(\Magento\Indexer\Model\Indexer\Collection::class, $collection->loadData());
    }
}
