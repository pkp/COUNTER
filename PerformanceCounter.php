<?php

/**
 * Copyright (c) 2015 University of Pittsburgh
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/**
 * COUNTER Reports classes, release 4.1
 * Represents the COUNTER XSD schema in class form
 *
 * @link http://www.niso.org/schemas/sushi
 *
 * @author Clinton Graham, University of Pittsburgh Library System, University of Pittsburgh <ctgraham@pitt.edu> +1-412-383-1057
 * @copyright 2015 University of Pittsburgh
 * @license http://www.gnu.org/licenses/gpl-2.0.html GPL 2.0 or later
 *
 * @package COUNTER
 *
 * @version 0.3
 */

namespace COUNTER;

/**
 * COUNTER performance counter class
 */
class PerformanceCounter extends ReportBuilder
{
    /**
     * @var string PerformanceCounter element "MetricType"
     */
    private $metricType;
    /**
     * @var int PerformanceCounter element "Count"
     */
    private $count;

    /**
     * Construct the object
     *
     * @param string $metricType
     * @param int $count
     *
     * @throws \Exception
     */
    public function __construct($metricType, $count)
    {
        $this->metricType = $this->validateString($metricType);
        if (!in_array($metricType, $this->getMetricTypes())) {
            throw new \Exception('Invalid metric type: ' . $metricType);
        }
        $this->count = $this->validatePositiveInteger($count);
    }

    /**
     * Construct the object from an array
     *
     * @param array $array Hash of key-values
     *
     * @throws \Exception
     *
     * @return self
     */
    public static function build($array)
    {
        if (is_array($array)) {
            if (isset($array['MetricType']) && isset($array['Count'])) {
                // Nicely structured associative array
                return new self($array['MetricType'], $array['Count']);
            }
            if (count(array_keys($array)) == 1 && parent::isAssociative($array)) {
                // Loosely structured associative array (type => count)
                foreach ($array as $k => $v) {
                    return new self($k, $v);
                }
            }
        }
        parent::build($array);
    }

    /**
     * Output this object as a DOMDocument
     *
     * @return \DOMDocument
     */
    public function asDOMDocument()
    {
        $doc = new \DOMDocument('1.0', 'utf-8');
        $root = $doc->appendChild($doc->createElement('Instance'));
        $root->appendChild($doc->createElement('MetricType'))->appendChild($doc->createTextNode($this->metricType));
        $root->appendChild($doc->createElement('Count'))->appendChild($doc->createTextNode($this->count));
        return $doc;
    }
}