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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


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

        // 创建一个新的Excel对象
        $spreadsheet = new Spreadsheet();

        // 获取当前活动的sheet
        $sheet = $spreadsheet->getActiveSheet();

        // 在第一行设置标题
        $sheet->setCellValue('A1', '标题');
        $sheet->setCellValue('B1', '副标题');
        $sheet->setCellValue('C1', '图片');
        $sheet->setCellValue('D1', '内容');
        $sheet->setCellValue('E1', '点击数');
        $sheet->setCellValue('F1', '推荐值');
        $sheet->setCellValue('G1', '作者');
        $sheet->setCellValue('H1', '网页标题');
        $sheet->setCellValue('I1', '网页关键词');
        $sheet->setCellValue('J1', '网页描述');

        // 填充数据
        $row = 2;
        foreach ($list as $row_data) {
            $sheet->fromArray($row_data, null, 'A' . $row);
            $row++;
        }

        // 设置文件名
        $filename = 'fileName.xlsx';
        // 创建Excel文件
        $writer = new Xlsx($spreadsheet);
        $writer->save($filename);

        // 下载文件
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
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