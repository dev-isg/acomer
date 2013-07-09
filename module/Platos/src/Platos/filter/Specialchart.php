<?php

namespace Platos\filter;//Zend\Filter;

use Zend\Stdlib\StringUtils;
use Zend\Filter\AbstractFilter;

class Specialchart extends AbstractFilter
{
    /**
     * Defined by Zend\Filter\FilterInterface
     *
     * Returns the string $value, removing all but digit characters
     *
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        return 'hola mundo';
        return htmlspecialchars($value);//preg_replace($pattern, '', (string) $value)
    }
}