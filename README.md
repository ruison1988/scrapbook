## 小说爬虫下载器（PHP）支持断点下载！！！

自己搭建好本地PHP运行环境

1. 导入==install.sql==文件创建数据库
2. 修改==index.php==里面的以下参数
```
//章节的选择器css语法
define('PRG_sb_section','div.article_texttitleb a');
//文章的选择器css语法
define('PRG_TITLE','div.book_content_text h1');
define('PRG_CONTENT','div#book_text');

//小说地址
protected $url = 'https://www.cmxsw.com';
protected $parm = '/29/29766/';
//过滤不要的内容
protected $filter = [
	'&nbsp;',
	'  ',
	'一秒记住【草莓小说网 www.cmxsw.Com 】，无弹窗，更新快，免费阅读！',
	'手机用户请浏览 http://m.cmxsw.Com阅读，更优质的阅读体验，书架与电脑版同步。',
];
//数据库配置
protected $db_config = [
	'host'   => '127.0.0.1',
	'port'   => '3306',
	'user'   => 'root',
	'passwd' => '123456',
	'dbname' => 'scrapbook',
];
```
3.运行==php index.php==即可在当前目录下生成book.txt文件

注意：如果你命令行下运行不了php说明没有在环境变量里面添加php的执行文件地址,将php.exe文件的地址拷贝到以下位置，然后重新打开cmd即可运行。

==在我的电脑-属性-高级系统设置-环境变量-PATH==