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
 * COUNTER report item class
 */
class ReportItems extends ReportBuilder
{
    /**
     * @var ParentItem ReportItem element "ParentItem"
     */
    private $parentItem;
    /**
     * @var Identifier[] zero or more COUNTER\Identifier elements
     */
    private $itemIdentifier;
    /**
     * @var ItemContributor[] zero or more COUNTER\ItemContributor elements
     */
    private $itemContributor;
    /**
     * @var ItemDate[] zero or more COUNTER\ItemDate elements
     */
    private $itemDate;
    /**
     * @var ItemAttribute[] zero or more COUNTER\ItemAttribute elements
     */
    private $itemAttribute;
    /**
     * @var string ReportItem element "ItemPlatform"
     */
    private $itemPlatform;
    /**
     * @var string ReportItem element "ItemPublisher"
     */
    private $itemPublisher;
    /**
     * @var string ReportItem element "ItemName"
     */
    private $itemName;
    /**
     * @var string ReportItem element "ItemData"
     */
    private $itemDataType;
    /**
     * @var Metric[] one or more COUNTER\Metric elements
     */
    private $itemPerformance;

    /**
     * Construct the object
     *
     * @param string $itemPlatform
     * @param string $itemName
     * @param string $itemDataType
     * @param Metric[] $itemPerformance COUNTER\Metric array
     * @param ParentItem $parentItem optional COUNTER\ParentItem
     * @param Identifier[] $itemIdentifiers optional COUNTER\Identifier array
     * @param ItemContributor[] $itemContributors optional COUNTER\ItemContributor array
     * @param ItemDate[] $itemDates optional COUNTER\ItemDate array
     * @param ItemAttribute[] $itemAttributes optional COUNTER\ItemAttribute array
     * @param string $itemPublisher optional
     *
     * @throws \Exception
     */
    public function __construct($itemPlatform, $itemName, $itemDataType, $itemPerformance, $parentItem = null, $itemIdentifiers = [], $itemContributors = [], $itemDates = [], $itemAttributes = [], $itemPublisher = '')
    {
        foreach (['itemPlatform', 'itemPublisher', 'itemName', 'itemDataType'] as $arg) {
            $this->$arg = $this->validateString($$arg);
        }
        if (!in_array($itemDataType, $this->getItemDataTypes())) {
            throw new \Exception('Invalid item data type: ' . $itemDataType);
        }
        $this->itemPerformance = $this->validateOneOrMoreOf($itemPerformance, 'Metric');
        $this->parentItem = $this->validateZeroOrOneOf($parentItem, 'ParentItem');
        $this->itemIdentifier = $this->validateZeroOrMoreOf($itemIdentifiers, 'Identifier');
        $this->itemContributor = $this->validateZeroOrMoreOf($itemContributors, 'ItemContributor');
        $this->itemDate = $this->validateZeroOrMoreOf($itemDates, 'ItemDate');
        $this->itemAttribute = $this->validateZeroOrMoreOf($itemAttributes, 'ItemAttribute');
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
            if (isset($array['ItemPlatform']) && isset($array['ItemName']) && isset($array['ItemDataType']) && isset($array['ItemPerformance'])) {
                // Nicely structured associative array
                $performance = parent::buildMultiple('COUNTER\Metric', $array['ItemPerformance']);
                $ids = parent::buildMultiple('COUNTER\Identifier', $array['ItemIdentifier'] ?? []);
                $contributors = parent::buildMultiple('COUNTER\ItemContributor', $array['ItemContributor'] ?? []);
                $dates = parent::buildMultiple('COUNTER\ItemDate', $array['ItemDate'] ?? []);
                $attributes = parent::buildMultiple('COUNTER\ItemAttribute', $array['ItemAttribute'] ?? []);
                return new self(
                    $array['ItemPlatform'],
                    $array['ItemName'],
                    $array['ItemDataType'],
                    $performance,
                    isset($array['ParentItem']) ? ParentItem::build($array['ParentItem']) : null,
                    $ids,
                    $contributors,
                    $dates,
                    $attributes,
                    $array['ItemPublisher'] ?? ''
                );
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
        $root = $doc->appendChild($doc->createElement('ReportItems'));
        if ($this->parentItem) {
            $root->appendChild($doc->importNode($this->parentItem->asDOMDocument()->documentElement, true));
        }
        if ($this->itemIdentifier) {
            foreach ($this->itemIdentifier as $id) {
                $root->appendChild($doc->importNode($id->asDOMDocument()->documentElement, true));
            }
        }
        if ($this->itemContributor) {
            foreach ($this->itemContributor as $contrib) {
                $root->appendChild($doc->importNode($contrib->asDOMDocument()->documentElement, true));
            }
        }
        if ($this->itemDate) {
            foreach ($this->itemDate as $date) {
                $root->appendChild($doc->importNode($date->asDOMDocument()->documentElement, true));
            }
        }
        if ($this->itemAttribute) {
            foreach ($this->itemAttribute as $attrib) {
                $root->appendChild($doc->importNode($attrib->asDOMDocument()->documentElement, true));
            }
        }
        $root->appendChild($doc->createElement('ItemPlatform'))->appendChild($doc->createTextNode($this->itemPlatform));
        if ($this->itemPublisher) {
            $root->appendChild($doc->createElement('ItemPublisher'))->appendChild($doc->createTextNode($this->itemPublisher));
        }
        $root->appendChild($doc->createElement('ItemName'))->appendChild($doc->createTextNode($this->itemName));
        $root->appendChild($doc->createElement('ItemDataType'))->appendChild($doc->createTextNode($this->itemDataType));
        foreach ($this->itemPerformance as $perf) {
            $root->appendChild($doc->importNode($perf->asDOMDocument()->documentElement, true));
        }
        return $doc;
    }
}