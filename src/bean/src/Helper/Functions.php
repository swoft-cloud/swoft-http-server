<?php
if (!function_exists('bean')) {
    /**
     * Get bean by name
     *
     * @param string $name Bean name Or alias Or class name
     *
     * @return object
     * @throws ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    function bean(string $name)
    {
        return \Swoft\Bean\BeanFactory::getBean($name);
    }
}

if (!function_exists('container')) {
    /**
     * Get container
     *
     * @return \Swoft\Bean\Container
     */
    function container(): \Swoft\Bean\Container
    {
        return \Swoft\Bean\Container::getInstance();
    }
}