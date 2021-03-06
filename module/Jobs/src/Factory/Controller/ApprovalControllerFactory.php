<?php
/**
 * YAWIK
 *
 * @filesource
 * @copyright https://yawik.org/COPYRIGHT.php
 * @license       MIT
 */

namespace Jobs\Factory\Controller;

use Interop\Container\ContainerInterface;
use Jobs\Controller\ApprovalController;
use Jobs\Repository;
use Laminas\Mvc\Controller\ControllerManager;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class ApprovalControllerFactory implements FactoryInterface
{

    /**
     * Create an ApprovalController
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     *
     * @return ApprovalController
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $searchForm = $container->get('forms')
                                ->get('Jobs/ListFilterAdmin');

        /* @var $jobRepository Repository\Job */
        $jobRepository = $container->get('repositories')->get('Jobs/Job');

        return new ApprovalController($jobRepository, $searchForm);
    }
}
