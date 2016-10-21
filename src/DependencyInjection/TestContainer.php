<?php

namespace RDV\SymfonyContainerMocks\DependencyInjection;

use Symfony\Component\DependencyInjection\Container;

class TestContainer extends Container
{
    /**
     * @var array
     */
    protected $mocked = array();

    /**
     * @param string $id The service identifier
     * @param object $mock replace service with this object
     */
    public function mock($id, $mock)
    {
        if (array_key_exists($id, $this->mocked)) {
            throw new \InvalidArgumentException('This service already mocked and can have references');
        }

        $this->mocked[$id] = $mock;
    }

    /**
     * Remove all mocked services
     */
    public function tearDown()
    {
        $this->mocked = array();
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        if (interface_exists('Symfony\Component\DependencyInjection\ResettableContainerInterface')
            && $this instanceof \Symfony\Component\DependencyInjection\ResettableContainerInterface) {
            parent::reset();
        }
        $this->mocked = array();
    }

    /**
     * @param string $id
     */
    public function unMock($id)
    {
        unset($this->mocked[$id]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($id, $invalidBehavior = self::EXCEPTION_ON_INVALID_REFERENCE)
    {
        if (array_key_exists($id, $this->mocked)) {
            return $this->mocked[$id];
        }

        return parent::get($id, $invalidBehavior);
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        if (array_key_exists($id, $this->mocked)) {
            return true;
        }

        return parent::has($id);
    }

    /**
     * {@inheritdoc}
     */
    public function initialized($id)
    {
        if (array_key_exists($id, $this->mocked)) {
            return true;
        }

        return parent::initialized($id);
    }

    /**
     * @return array
     */
    public function getMockedServices()
    {
        return $this->mocked;
    }

    /**
     * @param string $service
     * @return string
     * @throws \BadMethodCallException
     */
    public function detectClass($service)
    {
        return DefinitionLoader::getClassName($service, $this);
    }
}

