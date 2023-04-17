<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018\7\24 0024
 * Time: 17:10
 */

namespace think;


class Csv
{
    //导出csv文件
    public function put_csv($list,$title)
    {
        $file_name = "exam".time().".xls";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename='.$file_name );
        header('Cache-Control: max-age=0');
        $file = fopen('php://output',"a");
        $limit = 1000;
        $calc = 0;
        foreach ($title as $v){
            $tit[] = iconv('UTF-8', 'GB2312//IGNORE',$v);
        }
        fputcsv($file,$tit);
        foreach ($list as $v){
            $calc++;
            if($limit == $calc){
                ob_flush();
                flush();
                $calc = 0;
            }
            foreach($v as $t){
                $tarr[] = iconv('UTF-8', 'GB2312//IGNORE',$t);
            }
            fputcsv($file,$tarr);
            unset($tarr);
        }
        unset($list);
        fclose($file);
        exit();
    }

    // csv导入,此格式每次最多可以处理1000条数据
    public function input_csv($csv_file) {
        $result_arr = array ();
        $i = 0;
        while($data_line = fgetcsv($csv_file,10000)) {
            if ($i == 0) {
                $GLOBALS ['csv_key_name_arr'] = $data_line;
                $i ++;
                continue;
            }
            foreach($GLOBALS['csv_key_name_arr'] as $csv_key_num => $csv_key_name ) {
                $result_arr[$i][$csv_key_name] = $data_line[$csv_key_num];
            }
            $i++;
        }
        return $result_arr;
    }
}