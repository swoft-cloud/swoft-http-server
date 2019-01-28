<?php

namespace Swoft\Bean\Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation\Attribute;
use Doctrine\Common\Annotations\Annotation\Attributes;
use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Bean
 *
 * @Annotation
 * @Target("CLASS")
 * @Attributes({
 *     @Attribute("name", type="string"),
 *     @Attribute("scope", type="string"),
 *     @Attribute("alias", type="string"),
 * })
 *
 * @since 2.0
 */
class Bean
{
    /**
     * Singleton bean
     */
    const SINGLETON = 'singleton';

    /**
     * New bean
     */
    const PROTOTYPE = 'prototype';

    /**
     * Object pool
     */
    const POOL = 'pool';

    /**
     * New bean from every request
     */
    const REQUEST = 'request';

    /**
     * Bean name
     *
     * @var string
     */
    private $name = '';

    /**
     * Bean scope
     *
     * @var string
     * @Enum({Bean::SINGLETON, Bean::PROTOTYPE, Bean::REQUEST})
     */
    private $scope = self::SINGLETON;

    /**
     * Bean alias
     *
     * @var string
     */
    private $alias = '';

    /**
     * Default object pool size
     *
     * @var int
     */
    private $size = 100;

    /**
     * Bean constructor.
     *
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }
        if (isset($values['name'])) {
            $this->name = $values['name'];
        }
        if (isset($values['scope'])) {
            $this->scope = $values['scope'];
        }
        if (isset($values['alias'])) {
            $this->alias = $values['alias'];
        }
        if (isset($values['size'])) {
            $this->size = $values['size'];
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }
}