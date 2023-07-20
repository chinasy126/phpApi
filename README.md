# adminAPI

运行要求
> ThinkPHP5的运行环境要求PHP5.4以上。

application 文件夹
```bash
api文件夹:
   controller 实现接收返回数据
   model 实现数据读写
   
route文件夹：配置接口访问路径对应实现方法

config.php 文件 项目配置文件

database.php 文件
    #服务器地址
    'hostname'        => '127.0.0.1',
    # 数据库名
    'database'        => 'mydb',
    # 用户名
    'username'        => 'root',
    # 密码
    'password'        => 'root',
    # 端口
    'hostport'        => '3306',
    
route.php 文件用于配置路由,引用路由文件
```

public 文件夹
```bash
uploads文件夹存放前端上传的文件
index.php 项目跟文件初始访问文件
```

runtime 文件夹
```bash
缓存文件夹
```
thinkphp 文件夹
```bash
核心项目文件
```
vendor 文件夹
```bash
第三放依赖文件
```

项目数据库
```bash
目录中的mydb.sql文件
```
