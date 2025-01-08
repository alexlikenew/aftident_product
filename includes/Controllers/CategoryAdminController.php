<?php


namespace Controllers;


class CategoryAdminController extends CategoryController
{
    public function __construct($table = 'categories', $parent = false, $module_url = false)
    {

        parent::__construct($table, $parent, $module_url);
    }

    public function createHtmlSelect($title, $type = 'id', $selected = '', $onChange = '', $path_id = '', $id= '', $multiple = false){
        switch ($type) {
            case 'id';
                break;
            case 'title_url';
                break;
            default:
                $type = 'id';
        }

        $subTree = $this->getSubtree($path_id);

        $html = '<select ';
        if($id)
            $html.= 'id="'.$id.'" ';

        $html .= ' class="form-select ';

        if($multiple)
            $html .= ' select-multiple ';
        $html .= '" name="' . $title;
        if($multiple)
            $html .= '[]';
        $html .='" onchange="this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor; ' . $onChange.' " ';
        if($multiple)
            $html .= " multiple ";

        $html .= 'class="form-select" name="' . $title . '" onchange="this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor; ' . $onChange . '">' . "\n";

        $html .= $this->createOption($subTree, $type, $selected);
        $html .= '</select>';

        return $html;
    }

    public function create(array $post, array $files = null) {

        $post['order'] = $this->getMaxOrder(id:$post['category_id']) + 1;

        $post['path_id'] = $this->getPathById($post['category_id']);
        $post['depth'] = (empty($post['path_id']) ? 0 : substr_count($post['path_id'], '.')) + 1;

        $id = parent::create($post, $files);

        if($id){
            $this->model->updatePath($id);
        }
        return $id;
    }

    public function update(array $data, array $files = null):bool{
        //dd($data);
        $data['active'] = (isset($data['lang_active']) && !empty($data['lang_active'])) ? 1 : 0;

        return parent::update($data);

    }

    public function updateDescription($data){
        foreach($data['title'] as $langId=>$title){
            $this->model->updateDescription($data, $langId);
        }

        foreach($data['content'] as $langId=>$content){
            $this->model->updateContent($data['id'], $langId, $content, $data['lang_active'][$langId] ? true : false);
        }
        
    }
}
