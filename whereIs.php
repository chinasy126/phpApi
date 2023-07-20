<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>根据内容查询所属哪张表</title>
</head>
<body style="padding:10px;">

<h3>根据内容查询所属哪张表</h3>
<div>
    通过一个<strong>值</strong>查询出来是哪张表存在这个内容</div>

<div style=" padding-top:10px;">
    <form id="form1" name="form1" method="post" action="">
        模糊搜索值: <input type="text" name="searchKeyWord" id="searchKeyWord"
                      value="<?php echo $_POST['searchKeyWord']; ?>"/>
        <input type="submit" name="button" id="button" value="搜索"/>
    </form>
</div>


<table width="200" border="1" cellpadding="1" cellspacing="1" bordercolor="#CCCCCC" style=" margin-top:10px;">
    <tr>
        <td>所属表名</td>
    </tr>

    <?php

    $searchKeyWord = $_POST['searchKeyWord'];
    if (!empty($searchKeyWord)) {

        // 数据库地址
        $hostname = 'localhost';
        // 数据库用户名
        $username = 'root';
        // 数据库密码
        $password = 'root';
        // 数据库表明
        $database = 'mydb';
        // 搜索的关键字
        $searchKeyWord = $searchKeyWord;

        $link = mysqli_connect($hostname, $username, $password, $database);
        $tables = mysqli_query($link, "SHOW TABLES"); //展开所有表

        while ($row = mysqli_fetch_array($tables)) {
            $table_name = $row[0];
            $fields = mysqli_query($link, "SHOW COLUMNS FROM " . $table_name); // 展开所有字段

            while ($rf = mysqli_fetch_array($fields)) {
                $col = $rf[0];
                $table = $table_name;
                $where[] = $col . " like '%" . $searchKeyWord . "%'"; // 条件
            }

            $condition = ' where ' . implode(' or ', $where); //查询条件
            unset($where);
            // 执行查询
            $sql = 'select * from ' . $table_name . $condition; // sql 数组
            //查询结果
            $query = mysqli_query($link, $sql);
            if ($query != false) {
                $list = mysqli_fetch_array($query);
                if (!empty($list)) {
                    // echo '<pre>';
                    array_unshift($list, $table);
                    // var_dump($list);
                    echo '
                       <tr>
                        <td>
                        <strong>' . $list[0] . '</strong>
                        </td>
                      </tr>
		            ';
                }
            }
        }
        mysqli_close($link);
    }
    ?>
</table>
</body>
</html>
