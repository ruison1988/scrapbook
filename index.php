<?php

//小说别名
define('BOOK_NAME','book1');
//章节的选择器css语法
define('PRG_sb_section','div.article_texttitleb a');
//文章的选择器css语法
define('PRG_TITLE','div.book_content_text h1');
define('PRG_CONTENT','div#book_text');

include('simple_html_dom.php');
include('Database.php');

// ignore_user_abort(true);    //关掉浏览器，PHP脚本也可以继续执行.
set_time_limit(0);          // 通过set_time_limit(0)可以让程序无限制的执行下去
error_reporting(0);         //禁用错误报告，防止程序中断
header("content-type:text/html;charset=gbk");
class Index {
    //启动数据库
    protected $mysql;
    protected $index = 0;
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

    public function __construct() {
		//数据库配置
        $this->mysql = new MMysql ($this->db_config);
        //清数据库k
        // $this->mysql->doSql('DELETE FROM sb_section');
        $res = $this->mysql->where("book = '".BOOK_NAME."' and title<>'' ")->order('id desc')->limit(1)->select('sb_section');
        $this->index = !empty($res[0]) ? $res[0]['index']+1 : 0;
    }

    public function main() {
        $html = file_get_html($this->url . $this->parm);
        //写入文件
        $fp = fopen('./book.txt', 'a');
        $items = $html->find(PRG_sb_section);
        $curindex = 0;
        //记录当前章节
        foreach ($items as $k => $e) {
            if ($k < $this->index)
                continue;
            $link = $e->href;
            $title = $e->innertext;
            $title = mb_convert_encoding($title, 'GBK', 'UTF8');
            try {
                $this->scrap($link, $fp, $k);
            } catch (\Throwable $th) {
                //记录报错的章节
                $res = $this->mysql->insert('sb_section', ['book' => BOOK_NAME, 'index' => $k]);
                print_r("error!! try again: {$title}\n\r");
                sleep(5);
                try {
                    $this->scrap($link, $fp, $k);
                } catch (\Throwable $th) {
                    //记录报错的章节
                    $res = $this->mysql->insert('sb_section', ['book' => BOOK_NAME, 'index' => $k]);
                    print_r("error!! end: {$title}\n\r");
                    break;
                }
            }
            $curindex++;
        }
        //关闭文件
        fclose($fp);
        if ($curindex >= count($items))
            print_r("download successfully!!!");
    }

    protected function scrap($link, $fp, $k) {
        print_r("Scraping {$this->url}{$link} \n\r");
        $data = file_get_html($this->url . $link);
        $title = $data->find(PRG_TITLE, 0)->innertext;
        if(!$title) $title = $data->find(PRG_TITLE, 0)->plaintext;
        $title2 = mb_convert_encoding($title, 'UTF8', 'GBK');
        $this->mysql->insert('sb_section', ['book' => BOOK_NAME, 'index' => $k, 'title'=>trim($title2)]);
        if(!$title){
            throw new Exception('文章内容丢失','101');
            return;
        }
        $con = $data->find(PRG_CONTENT, 0)->plaintext;
        //过滤不要的内容
		foreach($this->filter as $k=>$v){
			$con = str_replace($v, '', $con);
		}
        $con = mb_convert_encoding($con, 'GBK', 'UTF8');
        $sec = $title . "\n\r\n\r" . $con . "\n\r\n\r\n\r";
        //给文件写入内容
        print_r("Writting start {$title} \n\r");
        fwrite($fp, $sec);
        print_r("Writting was done!\n\r");
    }

}

$obj = new index();
$obj->main();