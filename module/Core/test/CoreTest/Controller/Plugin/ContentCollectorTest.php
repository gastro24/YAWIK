<?php
/**
 * YAWIK
 *
 * @filesource
 * @license MIT
 * @copyright https://yawik.org/COPYRIGHT.php
 */

namespace CoreTest\Controller\Plugin;

use PHPUnit\Framework\TestCase;

use Core\Controller\AbstractCoreController;
use Core\Controller\Plugin\ContentCollector;
use Core\EventManager\EventManager;
use Laminas\EventManager\EventInterface;
use Laminas\View\Model\ViewModel;

/**
 * Class ContentCollectorTest
 *
 * @covers \Core\Controller\Plugin\ContentCollector
 * @package CoreTest\Controller\Plugin
 */
class ContentCollectorTest extends TestCase
{
    public function testTriggerThrowException()
    {
        $event = $this->createMock(EventInterface::class);
        $this->expectException(\InvalidArgumentException::class);
        $target = new ContentCollector();
        $target->trigger($event);
    }

    public function testTrigger()
    {
        $event = $this->createMock(EventInterface::class);
        $events = $this->createMock(EventManager::class);

        $controller = $this->createMock(AbstractCoreController::class);
        $controller->expects($this->any())
            ->method('getEventManager')
            ->willReturn($events)
        ;

        $viewModel = new ViewModel();
        $events->expects($this->any())
            ->method('trigger')
            ->with($event, 'some_target')
            ->willReturn([
                'test_template',$viewModel
            ])
        ;


        $target = new ContentCollector();
        $target->setController($controller);
        $target->setTemplate('some_template');
        $target->captureTo('some_path');

        /* @var \Laminas\View\Model\ViewModel[] $childs */
        $output = $target->trigger($event, 'some_target');
        $childs = $output->getChildren();
        $this->assertInstanceOf(ViewModel::class, $output);
        $this->assertEquals(
            'test_template',
            $childs[0]->getTemplate()
        );

        $this->assertEquals(
            'some_path1',
            $childs[1]->captureTo()
        );
    }
}
