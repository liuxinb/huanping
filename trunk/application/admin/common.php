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

//字符串截取
function truncate($data,$start=0,$len=80,$etc='...',$magic=true){
    if ($len == '') $len = strlen($data);
    if ($start != 0){
        $startv = ord(substr($data,$start,1));
        if ($startv >= 128){
            if ($startv < 192){
                for ($i=$start-1;$i>0;$i--){
                    $tempv = ord(substr($data,$i,1));
                    if ($tempv >= 192) break;
                }
                $start = $i;
            }
        }
    }
    $alen = $blen = $realnum = $length = 0;
    for ($i=$start;$i<strlen($data);$i++){
        $ctype = $cstep = 0;
        $cur = substr($data,$i,1);
        if ($cur == '&'){
            if (substr($data,$i,4) == '&lt;'){
                $cstep = 4;
                $length += 4;
                $i += 3;
                $realnum ++;
                if ($magic) $alen ++;
            }elseif (substr($data,$i,4) == '&gt;'){
                $cstep = 4;
                $length += 4;
                $i += 3;
                $realnum ++;
                if ($magic) $alen ++;
            }elseif (substr($data,$i,5) == '&amp;'){
                $cstep = 5;
                $length += 5;
                $i += 4;
                $realnum ++;
                if ($magic)$alen ++;
            }elseif (substr($data,$i,6) == '&quot;'){
                $cstep = 6;
                $length += 6;
                $i += 5;
                $realnum ++;
                if ($magic) $alen ++;
            }elseif (preg_match("/&#(/d+);?/i",substr($data,$i,8),$match)){
                $cstep = strlen($match[0]);
                $length += strlen($match[0]);
                $i += strlen($match[0])-1;
                $realnum ++;
                if ($magic){
                    $blen ++;
                    $ctype = 1;
                }
            }
        }else{
            if (ord($cur)>=252){
                $cstep = 6;
                $length += 6;
                $i += 5;
                $realnum ++;
                if ($magic){
                    $blen ++;
                    $ctype = 1;
                }
            }elseif (ord($cur)>=248){
                $cstep = 5;
                $length += 5;
                $i += 4;
                $realnum ++;
                if ($magic){
                    $ctype = 1;
                    $blen ++;
                }
            }elseif (ord($cur)>=240){
                $cstep = 4;
                $length += 4;
                $i += 3;
                $realnum ++;
                if ($magic){
                    $ctype = 1;
                    $blen ++;
                }
            }elseif (ord($cur)>=224){
                $cstep = 3;
                $length += 3;
                $i += 2;
                $realnum ++;
                if ($magic){
                    $ctype = 1;
                    $blen ++;
                }
            }elseif (ord($cur)>=192){
                $cstep = 2;
                $length += 2;
                $i += 1;
                $realnum ++;
                if ($magic){
                    $ctype = 1;
                    $blen ++;
                }
            }elseif (ord($cur)>=128){
                $length += 1;
            }else{
                $cstep = 1;
                $length +=1;
                $realnum ++;
                if ($magic) ord($cur) >= 65 && ord($cur) <= 90 ? $blen++ : $alen++;
            }
        }
        if ($magic){
            if (($blen*2+$alen) == ($len*2)) break;
            if (($blen*2+$alen) == ($len*2+1)){
                if ($ctype == 1){
                    $length -= $cstep;
                    break;
                }else{
                    break;
                }
            }
        }else{
            if ($realnum == $len) break;
        }
    }
    return strlen($data)<=$length ? $data : substr($data,$start,$length).'...';
}

