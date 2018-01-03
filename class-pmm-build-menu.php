<?php

class PMM_Build_Menu {
    
    public $menu_id = 0;
    
    public $menu_object_id = 0;
    
    public $menu_items = '';
        
    public function __construct($menu_id=0) {
        $this->menu_id = $menu_id;
        
        $menu_object = wp_get_nav_menu_object($menu_id);
        
        if (!isset($menu_object->term_id))
            return;
            
        $this->menu_object_id = $menu_object->term_id;
    }
    
    public function display() {
        return $this->build_menu();
    }

    protected function build_menu() {
        $html='';
        $this->menu_items = wp_get_nav_menu_items($this->menu_object_id);
        $layout = $this->get_layout();
        
        $html.='<div id="pmm-menu-'.$this->menu_id.'" class="pmm-menu columns-'.count($layout).'">';
        
            foreach ($layout as $column => $blocks) :
                $html.=$this->add_column($column, $blocks);
            endforeach;
        
        $html.='</div>';

        return $html;
    }
    
    protected function get_layout() {
        $layout = array();
        
        // get column (as key) and array of blocks (as value).
        foreach ($this->menu_items as $item) :
            $layout[$item->pmm_column][] = $item->pmm_block;
        endforeach;
        
        // make blocks unique.
        foreach ($layout as $column => $blocks) :
            $layout[$column] = array_values( array_unique( $blocks ) );
        endforeach;
        
        return $layout;            
    }
    
    protected function add_column($id, $blocks) {
        $html='';
        
        $html.='<div id="pmm-column-'.$id.'" class="pmm-column">';            
            foreach ($blocks as $block) :
                $html.=$this->add_block($id, $block);
            endforeach;
        $html.='</div>';
        
        return $html;
    }


    protected function add_block($column_id, $block_id) {
        $html='';
        
        $html.='<div id="pmm-block-'.$column_id.'-'.$block_id.'" class="pmm-block">';
            $html.=$this->add_items($column_id, $block_id);
        $html.='</div>';
        
        return $html;
    }
    
    protected function add_items($column_id, $block_id) {
        $html='';
        $items=array();
        
        // get items in column and block.
        foreach ($this->menu_items as $menu_item) :
            if ($menu_item->pmm_column == $column_id && $menu_item->pmm_block == $block_id) :
                $items[] = $menu_item;
            endif;
        endforeach;
        
        if (empty($items))
            return;
            
        // double check order.
        usort($items, function($a, $b) {
           return $a->pmm_order - $b->pmm_order; 
        });

        foreach ($items as $item) :
            if (isset($item->pmm_item_type) && '' !== $item->pmm_item_type)
                $html.='<div class="pmm-item"><a href="'.get_permalink($item->ID).'">'.$item->title.'</a></div>';
        endforeach;
        
        return $html;
    }    

}