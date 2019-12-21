<?php
//https://api.bilibili.com/x/web-interface/archive/stat?aid=314
//https://api.bilibili.com/x/web-interface/view?aid=314
//{"code":0,"message":"0","ttl":1,"data":{"aid":314,"bvid":"","view":7445821,"danmaku":321466,"reply":47018,"favorite":267293,"coin":78902,"share":19520,"like":100638,"now_rank":0,"his_rank":12,"no_reprint":0,"copyright":2,"argue_msg":"","evaluation":""}}
//{"code":0,"message":"0","ttl":1,"data":{"bvid":"","aid":314,"videos":1,"tid":26,"tname":"音MAD","copyright":2,"pic":"http://i0.hdslb.com/bfs/archive/ae4340a8d98a8d63ade05ea96d1db6dd775a697b.jpg","title":"电磁炮真是太可爱了","pubdate":1345387488,"ctime":1497365740,"desc":"补档 原UP主 MadoKami sm8014721 弹幕万岁 \\^o^/","state":0,"attribute":32768,"duration":96,"rights":{"bp":0,"elec":0,"download":1,"movie":0,"pay":0,"hd5":0,"no_reprint":0,"autoplay":1,"ugc_pay":0,"is_cooperation":0,"ugc_pay_preview":0,"no_background":0},"owner":{"mid":57862,"name":"アイリス","face":"http://i1.hdslb.com/bfs/face/2bfa45669104f374807c3150f885c14f0da6cca3.jpg"},"stat":{"aid":314,"view":7445965,"danmaku":321472,"reply":47019,"favorite":267291,"coin":78902,"share":19520,"now_rank":0,"his_rank":12,"like":100641,"dislike":0,"evaluation":""},"dynamic":"","cid":3262388,"dimension":{"width":0,"height":0,"rotate":0},"no_cache":false,"pages":[{"cid":3262388,"page":1,"from":"vupload","part":"","duration":96,"vid":"","weblink":"","dimension":{"width":0,"height":0,"rotate":0}}],"subtitle":{"allow_submit":false,"list":[]}}}

$f = fopen('avdata.csv', 'a');
$log = fopen('log.log', 'a');
fwrite($f, "\xEF\xBB\xBF");//utf8支持

$header = array(
    'AV',//aid
    '标题',//title
    '简介',//desc
    'UP主',//owner -> mid
    '发布时间',//pubdate
    '视频数',//videos
    '视频类型',//tname
    '观看数',//stat -> view
    '弹幕数',//stat -> danmaku
    '评论数',//stat -> reply
    '收藏数',//stat -> favorite
    '硬币数',//stat -> coin
    '分享数',//stat -> share
    '点赞数',//stat -> like
    '现行排名',//stat -> now_rank
    '历史排名',//stat -> his_rank
    '其他信息'//dynamic
);

fputcsv($f, $header);
for ($aid = 1; $aid < 99999999; $aid++) {
    echo '正在获取'.$aid.PHP_EOL;
    $array=array();
    $anime = json_decode(file_get_contents('https://api.bilibili.com/x/web-interface/view?aid=' . $aid), true);
    if ($anime['code'] == 0) {
        $animedata = $anime['data'];
        $animewatch = $anime['data']['stat'];
        $array[]=$animedata['aid'];
        $array[]=$animedata['title'];
        $array[]=$animedata['desc'];
        $array[]=$animedata['owner']['name'].' <uid'.$animedata['owner']['mid'].'>';
        $array[]=date('Y-m-d H:i:s',$animedata['pubdate']);
        $array[]=$animedata['videos'];
        $array[]=$animedata['tname'];
        $array[]=$animewatch['view'];
        $array[]=$animewatch['danmaku'];
        $array[]=$animewatch['reply'];
        $array[]=$animewatch['favorite'];
        $array[]=$animewatch['coin'];
        $array[]=$animewatch['share'];
        $array[]=$animewatch['like'];
        $array[]=$animewatch['now_rank'];
        $array[]=$animewatch['his_rank'];
        $array[]=$animedata['dynamic'];
        fputcsv($f,$array);
        fputs($log, 'av'.$aid.' 《'.$animedata['title'].'》  Done!'.PHP_EOL);
    } else {
        $array = array(
            $aid,//aid
            '获取出错',
            $anime['message'].' ('.$anime['code'].')',//title
        );
        fputs($log, 'av'.$aid.'获取出错 '.$anime['message'].' ('.$anime['code'].')'.PHP_EOL);
        fputcsv($f,$array);
    }
}

fclose($f);
fclose($log);