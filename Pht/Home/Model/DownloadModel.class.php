<?php

namespace Home\Model;

use Think\Model;
class DownloadModel extends Model {
    //添加收藏
    public function add_download($data){
        $download = $this->data($data)->add();
        return $download;
    }

}