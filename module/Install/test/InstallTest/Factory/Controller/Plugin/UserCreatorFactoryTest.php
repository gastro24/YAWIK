<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

/** */
namespace InstallTest\Factory\Controller\Plugin;

use PHPUnit\Framework\TestCase;

use Auth\Entity\Filter\CredentialFilter;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ODM\MongoDB\Configuration;
use Doctrine\ODM\MongoDB\DocumentManager;
use Install\Factory\Controller\Plugin\UserCreatorFactory;
use Install\Filter\DbNameExtractor;
use Laminas\ServiceManager\Factory\FactoryInterface;

/**
 * Tests for \Install\Factory\Controller\Plugin\UserCreatorFactory
 *
 * @covers \Install\Factory\Controller\Plugin\UserCreatorFactory
 * @author Mathias Gelhausen <gelhausen@cross-solution.de>
 * @group Install
 * @group Install.Factory
 * @group Install.Factory.Controller
 * @group Install.Factory.Controller.Plugin
 */
class UserCreatorFactoryTest extends TestCase
{

    /**
     * @testdox Implements \Laminas\ServiceManager\FactoryInterface
     */
    public function testImplementsFactoryInterface()
    {
        $this->assertInstanceOf(FactoryInterface::class, new UserCreatorFactory());
    }

    public function testCreatesAnUserCreatorPluginInstance()
    {
        $filters = $this->getMockBuilder('\Laminas\Filter\FilterPluginManager')->disableOriginalConstructor()->getMock();
        $filters->expects($this->exactly(2))
                ->method('get')
                ->withConsecutive(
                    array(DbNameExtractor::class),
                    array(CredentialFilter::class)
                )
                ->will($this->onConsecutiveCalls(new DbNameExtractor(), new CredentialFilter()));


        $configuration = $this->createMock(Configuration::class);
        $dm = $this->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $dm->expects($this->once())
            ->method('getConfiguration')
            ->willReturn($configuration)
        ;
        $services = $this->getMockBuilder('\Laminas\ServiceManager\ServiceManager')->disableOriginalConstructor()->getMock();
        $services
            ->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                ['FilterManager'],
                ['doctrine.documentmanager.odm_default']
            )
            ->will($this->onConsecutiveCalls(
                $filters,
                $dm
            ))
        ;


        //$plugins = $this->getMockBuilder('\Laminas\Mvc\Controller\PluginManager')->disableOriginalConstructor()->getMock();
        //$plugins->expects($this->once())->method('getServiceLocator')->willReturn($services);

        $target = $this->getMockBuilder(UserCreatorFactory::class)
            ->setMethods(['createDocumentManager'])
            ->getMock()
        ;
        $target->expects($this->once())
            ->method('createDocumentManager')
            ->willReturn($dm)
        ;

        /* @var \Install\Factory\Controller\Plugin\UserCreatorFactory $target */
        $plugin = $target($services, 'some-name', ['connection' => 'some-connection']);

        $this->assertInstanceOf('\Install\Controller\Plugin\UserCreator', $plugin);
    }
}
