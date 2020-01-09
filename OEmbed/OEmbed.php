<?php

namespace Markup\OEmbedBundle\OEmbed;

/**
* A simple oEmbed object implementation.
*/
class OEmbed implements OEmbedInterface
{
    /**
     * @var array<string>
     */
    private static $knownTypes = ['photo', 'video', 'link', 'rich'];

    /**
     * The oEmbed type.
     *
     * @var string
     **/
    private $type;

    /**
     * @var array
     **/
    private $properties;

    /**
     * @var string|null
     **/
    private $embedProperty;

    /**
     * @param string $type          The oEmbed type property.
     * @param array  $properties    All the properties.
     * @param string $embedProperty
     **/
    public function __construct(string $type, array $properties, string $embedProperty = null)
    {
        if (!in_array($type, self::$knownTypes)) {
            throw new \InvalidArgumentException(
                sprintf('Tried to create an OEmbed instance with unknown type "%s".', $type)
            );
        }
        $this->type = $type;
        $this->properties = $properties;
        $this->embedProperty = $embedProperty;
    }

    /**
     * {@inheritdoc}
     **/
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     **/
    public function has($property)
    {
        return isset($this->properties[$property]);
    }

    /**
     * {@inheritdoc}
     **/
    public function get($property)
    {
        if (!$this->has($property)) {
            return null;
        }

        return $this->properties[$property];
    }

    /**
     * {@inheritdoc}
     **/
    public function all()
    {
        return $this->properties;
    }

    /**
     * {@inheritdoc}
     **/
    public function getEmbedCode()
    {
        if (null === $this->embedProperty || !$this->has($this->embedProperty)) {
            return null;
        }

        return $this->get($this->embedProperty);
    }

    /**
     * Gets the form to encode into JSON.
     *
     * @return array
     **/
    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'properties' => $this->properties,
            'embed_property' => $this->embedProperty,
        ];
    }
}
