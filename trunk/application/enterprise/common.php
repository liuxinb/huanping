<?php

//文件下载
function downFile($content,$filename){
    @ob_end_clean();
    header('Content-Encoding: none');
    header('Content-Type: '.(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE') ? 'application/octetstream' : 'application/octet-stream'));
    header('Content-Disposition: '.(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE') ? 'inline;' : 'attachment;').' filename='.$filename);
    header('Content-Length: '.strlen($content));
    header('Pragma: no-cache');
    header('Expires: 0');
    echo $content;
    $e = ob_get_contents();
//    @ob_end_clean();  //清楚缓冲区,避免乱码 ,但是在Linux下就会导致Excel导出不成功
}
