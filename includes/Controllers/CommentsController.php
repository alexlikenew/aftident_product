<?php


namespace Controllers;
use Models\Comments;

class CommentsController extends Controller
{
    protected $table;
    protected $model;

    public function __construct($table = 'comments'){
        $this->model = new Comments($table);
        parent::__construct();
    }

    public function getUserIP() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function create(array $data, array $files = null): int{
        $data['author'] = prepareString($data['author'], true);
        $data['title'] = prepareString($data['title'], true);
        $data['content'] = prepareString($data['content'], true);

        if (!LOGGED) {
            $data['author'] = '~' . $data['author'];
            $data['user_id'] = 0;
            $data['ip'] = $this->getUserIP();


        } else {
            $data['author'] = $data['author'];
            $data['user_id'] = $_SESSION['user']['id'];

            if ($data['ip'] != '') $data['ip']= '0.0.0.'.$data['ip'];
            else $data['ip']= '0.0.0.'.rand(0,250);
        }

        $id = $this->model->create($data);

        if($id){

        }
        $this->setInfo($GLOBALS['_PAGE_COMMENT_ADD']);
        return $id;
    }

    public function update(array $data): bool {
        $data['author'] = prepareString($data['author'], true);
        $data['title'] = prepareString($data['title'], true);
        $data['content'] = prepareString($data['content'], true);

        if($this->model->update($data)){
            $this->setInfo($GLOBALS['_PAGE_COMMENT_CHANGE']);
            return true;
        }
        else{
            $this->setError('Cannot update comment');
            return false;
        }
    }

    public function delete(int $id = null): bool {
        return $this->model->delete($id);
    }

    public function loadAll($group, $page = 1){
        $start = ($page - 1) * $this->limit_page;
        $data = $this->model->getAllComments($group, $start, $this->limit_page);

        return $data;
    }

}