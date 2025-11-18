<?php

class SimplePager {
    public $limit;
    public $page;
    public $item_count;
    public $page_count;
    public $result;
    public $count;

    public function __construct($query, $params = [], $perPage = 10, $page = 1) {
        global $_db;

        // Validate and set parameters
        $this->limit = is_numeric($perPage) ? max($perPage, 1) : 10;
        $this->page = is_numeric($page) ? max($page, 1) : 1;

        // Count total items (using a more reliable method for complex queries)
        $count_query = "SELECT COUNT(*) FROM ($query) AS total_count";
        $stm = $_db->prepare($count_query);
        $stm->execute($params);
        $this->item_count = (int)$stm->fetchColumn();

        // Calculate page count
        $this->page_count = $this->item_count > 0 ? ceil($this->item_count / $this->limit) : 1;

        // Calculate offset
        $offset = ($this->page - 1) * $this->limit;

        // Fetch paginated result
        $stm = $_db->prepare($query . " LIMIT $offset, $this->limit");
        $stm->execute($params);
        $this->result = $stm->fetchAll();

        // Count items in current page
        $this->count = count($this->result);
    }

    public function html($href = '', $attr = '') {
        if (!$this->result || $this->page_count <= 1) return '';

        $html = "<nav class='pager' $attr>";

        // Previous arrow
        if ($this->page > 1) {
            $prev = $this->page - 1;
            $html .= "<a href='?page=$prev&$href' class='pager-arrow'>&lt;</a> ";
        } else {
            $html .= "<span class='pager-arrow disabled'>&lt;</span> ";
        }

        // Always show first page
        if ($this->page > 3) {
            $html .= "<a href='?page=1&$href'>1</a> ";
            if ($this->page > 4) {
                $html .= "<span class='pager-ellipsis'>...</span> ";
            }
        }

        // Show surrounding pages (2 pages before and after current)
        $start = max(1, $this->page - 2);
        $end = min($this->page_count, $this->page + 2);
        
        for ($i = $start; $i <= $end; $i++) {
            $active = $i == $this->page ? 'active' : '';
            $html .= "<a href='?page=$i&$href' class='$active'>$i</a> ";
        }

        // Show last pages
        if ($this->page < $this->page_count - 2) {
            if ($this->page < $this->page_count - 3) {
                $html .= "<span class='pager-ellipsis'>...</span> ";
            }
            $html .= "<a href='?page=$this->page_count&$href'>$this->page_count</a> ";
        }

        // Next arrow
        if ($this->page < $this->page_count) {
            $next = $this->page + 1;
            $html .= "<a href='?page=$next&$href' class='pager-arrow'>&gt;</a>";
        } else {
            $html .= "<span class='pager-arrow disabled'>&gt;</span>";
        }

        $html .= "</nav>";

        return $html;
    }
}