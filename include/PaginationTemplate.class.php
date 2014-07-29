<?php
/**
 * Copyright 2014 Daniel Butum <danibutum at gmail dot com>
 *
 * This file is part of stkaddons
 *
 * stkaddons is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * stkaddons is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with stkaddons.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Class for pagination view
 */
class PaginationTemplate extends Template
{
    const PAGE_ARGUMENT = "p";

    const LIMIT_ARGUMENT = "l";

    /**
     * The total entries present
     * @var int
     */
    protected $totalItems = 0;

    /**
     * The current page
     * @var int
     */
    protected $currentPage = 1;

    /**
     * Items on page ratio
     * @var int
     */
    protected $itemsPerPage = 8;

    /**
     * The number of button visible, expect the first and last button
     * @var int
     */
    protected $numberButtons = 4;

    /**
     * The base url to build each button href/url
     * @var string
     */
    protected $pageUrl;

    /**
     * @param null $template_dir
     */
    public function __construct($template_dir = null)
    {
        parent::__construct("pagination.tpl", $template_dir);
    }

    /**
     * @param null $template_dir
     *
     * @return static
     */
    public static function get($template_dir = null)
    {
        return new static($template_dir);
    }

    /**
     * Get the current page number from the get params
     *
     * @param int $default_page the default page
     *
     * @return int
     */
    public static function getPageNumber($default_page = 1)
    {
        if (!empty($_GET[static::PAGE_ARGUMENT]))
        {
            return (int)$_GET[static::PAGE_ARGUMENT];
        }

        return $default_page;
    }

    /**
     * Get the items per page number from the get params
     *
     * @param int $default_limit the default number of items
     *
     * @return int
     */
    public static function getLimitNumber($default_limit = 8)
    {
        if (!empty($_GET[static::LIMIT_ARGUMENT]))
        {
            return (int)$_GET[static::LIMIT_ARGUMENT];
        }

        return $default_limit;
    }

    /**
     * Test the template by outputting consecutive
     *
     * @param int $totalItems
     */
    public static function testTemplate($totalItems = 30)
    {
        for ($perPage = 1; $perPage < $totalItems / 2; $perPage++)
        {
            for ($i = 1; $i <= ceil($totalItems / $perPage); $i++)
            {
                echo PaginationTemplate::get()->setCurrentPage($i)->setTotalItems($totalItems)->setItemsPerPage($perPage);
                echo "<br>";
            }
        }
    }

    /**
     * Build the template
     */
    protected function setup()
    {
        $totalPages = (int)ceil($this->totalItems / $this->itemsPerPage);
        $hasPagination = ($this->totalItems > $this->itemsPerPage); // do not paginate

        // check to not go over the limit
        if ($this->currentPage > $totalPages)
        {
            $this->currentPage = $totalPages;
        }

        // 0 means disabled
        $prevPage = ($this->currentPage === 1) ? 0 : $this->currentPage - 1;
        $nextPage = ($this->currentPage === $totalPages) ? 0 : $this->currentPage + 1;

        // set default page, if not set already
        if (!$this->pageUrl)
        {
            $this->pageUrl = Util::removeQueryArguments([static::PAGE_ARGUMENT], Util::getCurrentUrl());
        }

        // see if we build the ... on one direction or the other
        $buildLeft = ($this->currentPage - 1) > $this->numberButtons;

        $rightOffset = $this->currentPage - 1 + $this->numberButtons;
        $buildRight = ($rightOffset !== $totalPages) && ($rightOffset < $totalPages);

        if (!$buildLeft && !$buildRight) // both are false, should not happen, build right by default
        {
            $buildRight = true;
        }

        $pagination = [
            "has_pagination" => $hasPagination, // pagination is present
            "current_page"   => $this->currentPage,
            "prev_page"      => $prevPage,
            "next_page"      => $nextPage,
            "total_pages"    => $totalPages,
            "url"            => $this->pageUrl, // the base url to build each button
            "nr_buttons"     => $this->numberButtons, // the relative number of buttons present except the first and last
            "build_left"     => $buildLeft, // display '...' on the left
            "build_right"    => $buildRight // display '...' on the right
        ];
        $this->assign("pagination", $pagination);
    }

    /**
     * @param int $page
     *
     * @return $this
     */
    public function setCurrentPage($page)
    {
        $this->currentPage = $page;

        return $this;
    }

    /**
     * @param int $items
     *
     * @return $this
     */
    public function setTotalItems($items)
    {
        $this->totalItems = $items;

        return $this;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setPageUrl($url)
    {
        $this->pageUrl = $url;

        return $this;
    }

    /**
     * @param int $nr
     *
     * @return $this
     */
    public function setNumberButtons($nr)
    {
        $this->numberButtons = $nr;

        return $this;
    }

    /**
     * @param int $perPage
     *
     * @return $this
     */
    public function setItemsPerPage($perPage)
    {
        $this->itemsPerPage = $perPage;

        return $this;
    }
}
