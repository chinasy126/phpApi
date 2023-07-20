<?php


namespace app\api\controller\controllers;


use app\api\controller\Api;
use app\api\model\NewsModel;
use think\Controller;
use think\Csv;
use think\Db;
use think\Request;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Style_Alignment;

/**
 * Class Admin
 * @package app\api\controller\v1
 * 用户管理
 */
class NewsController extends Api
{
    private $postData;

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->postData = $this->request->param();
    }

    /**
     * @return 返回列表所需数据
     */
    public function getListOfData()
    {
        $postData = $this->request->param();
        $map = array();
        !empty($postData['title']) ? $map['title'] = ['like', "%" . $postData['title'] . "%"] : '';
        !empty($postData['update']) ? $map['update'] = $postData['update'] : '';
        $pageSize = $postData['pageSize'];
        $currentPage = $postData['currentPage'];
        $result = new NewsModel();
        $res = $result->getDataList($map, $currentPage, $pageSize);
        return $this->sendSuccess($res);
    }


    /**
     *  新增或者需修改数据
     * @return
     */
    public function saveOrUpdate()
    {
        $model = new NewsModel();
        $result = $model->insertNews($this->postData);
        return $this->sendSuccess($result);
    }

    /**
     * 删除新闻数据
     * @return
     */
    public function deleteNews()
    {
        $map = $this->postData;
        $result = Db::table('news')->where($map)->delete();
        return $this->sendSuccess($result);
    }

    /**
     *  导出新闻数据
     *
     */
    public function exportNews()
    {
        $map = $this->postData;
        if (!empty($map['id'])) {
            $list = Db::table('news')->whereIn('id', implode(',', $map['id']))
                ->field('title, fTitle, pic, contents, num, top,author,webtitle,webkey,webdes')
                ->select();
        } else {
            $list = Db::table('news')->
            field('title, fTitle, pic, contents, num, top,author,webtitle,webkey,webdes')
                ->select();
        }

//        // Create a new Excel workbook
        $objPHPExcel = new PHPExcel();
        $abcedf = abcdefg();
        $objPHPExcel->getActiveSheet()->setCellValue('A1', '标题');
        $objPHPExcel->getActiveSheet()->setCellValue('B1', '副标题');
        $objPHPExcel->getActiveSheet()->setCellValue('C1', '图片');
        $objPHPExcel->getActiveSheet()->setCellValue('D1', '内容');
        $objPHPExcel->getActiveSheet()->setCellValue('E1', '点击数');
        $objPHPExcel->getActiveSheet()->setCellValue('F1', '推荐值');
        $objPHPExcel->getActiveSheet()->setCellValue('G1', '作者');
        $objPHPExcel->getActiveSheet()->setCellValue('H1', '网页标题');
        $objPHPExcel->getActiveSheet()->setCellValue('I1', '网页关键词');
        $objPHPExcel->getActiveSheet()->setCellValue('J1', '网页描述');

        foreach ($list as $key => $value) {
            $num = $key + 2;
            $objPHPExcel->getActiveSheet()->setCellValue('A' . $num, $value['title']);
            $objPHPExcel->getActiveSheet()->setCellValue('B' . $num, $value['fTitle']);
            $objPHPExcel->getActiveSheet()->setCellValue('C' . $num, $value['pic']);
            $objPHPExcel->getActiveSheet()->setCellValue('D' . $num, $value['contents']);
            $objPHPExcel->getActiveSheet()->setCellValue('E' . $num, $value['num']);
            $objPHPExcel->getActiveSheet()->setCellValue('F' . $num, $value['top']);
            $objPHPExcel->getActiveSheet()->setCellValue('G' . $num, $value['author']);
            $objPHPExcel->getActiveSheet()->setCellValue('H' . $num, $value['webtitle']);
            $objPHPExcel->getActiveSheet()->setCellValue('I' . $num, $value['webkey']);
            $objPHPExcel->getActiveSheet()->setCellValue('J' . $num, $value['webdes']);
        }

        // Set the response headers
        $filename = 'example.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Create a PHPExcel Writer object and send the output to the client
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
//        exit;
    }

    /**
     * 导入数据
     */

    public function importNews()
    {

        $file = request()->file('file');
        $filename = $file->getRealPath();
        $inputFileType = PHPExcel_IOFactory::identify($filename);
        $reader = PHPExcel_IOFactory::createReader($inputFileType);
        $reader->setReadDataOnly(true);
        $objPHPExcel = $reader->load($filename);
        $sheet = $objPHPExcel->getActiveSheet();

        foreach ($sheet->getRowIterator() as $key => $row) {
            $rowData = array();
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE); // This loops through all cells, even if a cell value is not set.
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            if ($key != 1) {
                $data['title'] = $rowData[0];
                $data['fTitle'] = $rowData[1];
                $data['pic'] = $rowData[2];
                $data['contents'] = $rowData[3];
                $data['num'] = $rowData[4];
                $data['top'] = $rowData[5];
                $data['author'] = $rowData[6];
                $data['webtitle'] = $rowData[7];
                $data['webkey'] = $rowData[8];
                $data['webdes'] = $rowData[9];
                Db::table('news')->data($data)->insert();
            }

        }
        return $this->sendSuccess('ok');
    }

    public function newsBatchDelete()
    {
        $postData = $this->request->param();
        if (!empty($postData["id"]) && count($postData["id"]) > 0) {
            $map = array();
            $map["id"] = array("in", $postData["id"]);
            Db::name("news")->where($map)->delete();
        }
        return $this->sendSuccess("ok");
    }

}