<?php
/**
 * Cytracon
 *
 * This source file is subject to the Cytracon Software License, which is available at https://www.cytracon.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to https://magento.com for more information.
 *
 * @category  Cytracon
 * @package   Cytracon_UiBuilder
 * @copyright Copyright (C) 2018 Cytracon (https://www.cytracon.com)
 */
namespace Cytracon\UiBuilder\Data\Form\Element;

use Cytracon\UiBuilder\Data\Form;
use Cytracon\UiBuilder\Data\Form\AbstractForm;

class Collection implements \ArrayAccess, \IteratorAggregate
{
    /**
     * Elements storage
     *
     * @var array
     */
    private $_elements;

    /**
     * Elements container
     *
     * @var AbstractForm
     */
    private $_container;

    /**
     * Class constructor
     *
     * @param AbstractForm $container
     */
    public function __construct(AbstractForm $container)
    {
        $this->_elements = [];
        $this->_container = $container;
    }

    /**
     * Implementation of \IteratorAggregate::getIterator()
     *
     * @return \ArrayIterator
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new \ArrayIterator($this->_elements);
    }

    /**
     * Implementation of \ArrayAccess:offsetSet()
     *
     * @param mixed $key
     * @param mixed $value
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($key, $value)
    {
        $this->_elements[$key] = $value;
    }

    /**
     * Implementation of \ArrayAccess:offsetGet()
     *
     * @param mixed $key
     * @return AbstractElement
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($key)
    {
        return $this->_elements[$key];
    }

    /**
     * Implementation of \ArrayAccess:offsetUnset()
     *
     * @param mixed $key
     * @return void
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($key)
    {
        unset($this->_elements[$key]);
    }

    /**
     * Implementation of \ArrayAccess:offsetExists()
     *
     * @param mixed $key
     * @return boolean
     */
    #[\ReturnTypeWillChange]
    public function offsetExists($key)
    {
        return isset($this->_elements[$key]);
    }

    public function getContainer()
    {
        return $this->_container;
    }

    /**
     * Add element to collection
     *
     * @todo get it straight with $after
     * @param AbstractElement $element
     * @param bool|string $after
     * @return AbstractElement
     */
    public function add(AbstractElement $element, $after = false)
    {
        // Set the Form for the node
        if ($this->_container->getForm() instanceof Form) {
            $element->setContainer($this->_container);
            $element->setForm($this->_container->getForm());
        }

        if ($after === false) {
            $this->_elements[] = $element;
        } elseif ($after === '^') {
            array_unshift($this->_elements, $element);
        } elseif (is_string($after)) {
            $newOrderElements = [];
            foreach ($this->_elements as $index => $currElement) {
                if ($currElement->getId() == $after) {
                    $newOrderElements[] = $currElement;
                    $newOrderElements[] = $element;
                    $this->_elements = array_merge($newOrderElements, array_slice($this->_elements, $index + 1));
                    return $element;
                }
                $newOrderElements[] = $currElement;
            }
            $this->_elements[] = $element;
        }

        return $element;
    }

    /**
     * Sort elements by values using a user-defined comparison function
     *
     * @param mixed $callback
     * @return $this
     */
    public function usort($callback)
    {
        usort($this->_elements, $callback);
        return $this;
    }

    /**
     * Remove element from collection
     *
     * @param mixed $elementId
     * @return $this
     */
    public function remove($elementId)
    {
        foreach ($this->_elements as $index => $element) {
            if ($elementId == $element->getId()) {
                unset($this->_elements[$index]);
            }
        }
        // Renumber elements for further correct adding and removing other elements
        $this->_elements = array_merge($this->_elements, []);
        return $this;
    }

    /**
     * Count elements in collection
     *
     * @return int
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->_elements);
    }

    /**
     * Find element by ID
     *
     * @param mixed $elementId
     * @return AbstractElement
     */
    public function searchById($elementId)
    {
        foreach ($this->_elements as $element) {
            if ($element->getId() == $elementId) {
                return $element;
            }
        }
        return null;
    }
}
