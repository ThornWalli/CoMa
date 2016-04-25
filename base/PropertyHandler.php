<?php

namespace CoMa\Base;

class PropertyHandler
{

    public $sqlTable;

    private $properties = null;

    public function load()
    {
        return $this->properties;
    }

    public function save()
    {

    }

    public function set($properties)
    {
        $this->properties = $properties;;
        $this->save();
    }

    public function get()
    {
        if ($this->properties == null) {
            $this->properties = $this->load();
        }
        if (!is_array($this->properties)) {
            $this->properties = [];
        }
        return $this->properties;
    }

    public function remove()
    {
        global $wpdb;
        if ($this->isExist()) {
            $wpdb->delete(
                $this->sqlTable,
                ['page_id' => $this->pageId]
            );
        }
    }

}

?>