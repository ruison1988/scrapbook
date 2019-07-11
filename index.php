<?php
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
    protected $url = 'https://www.cmxsw.com';
    protected $parm = '/29/29766/';

    public function __construct() {
        $this->mysql = new MMysql ([
            'host'   => '127.0.0.1',
            'port'   => '3306',
            'user'   => 'root',
            'passwd' => '123456',
            'dbname' => 'bbb',
        ]);
        //清数据库k
        // $this->mysql->doSql('DELETE FROM current');
        $res = $this->mysql->limit(1)->order('id desc')->select('current');
        $this->index = !empty($res[0]) ? $res[0]['index'] : 0;
    }

    public function main() {
        $html = file_get_html($this->url . $this->parm);
        //写入文件
        $fp = fopen('./book.txt', 'a');
        $items = $html->find('div.article_texttitleb a');
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
                $res = $this->mysql->insert('current', ['index' => $k]);
                print_r("error!! try again: {$title}\n\r");
                sleep(5);
                try {
                    $this->scrap($link, $fp, $k);
                } catch (\Throwable $th) {
                    //记录报错的章节
                    $res = $this->mysql->insert('current', ['index' => $k]);
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
        $title = $data->find('div.book_content_text h1', 0)->innertext;
        if(!$title) $title = $data->find('div.book_content_text h1', 0)->plaintext;
        $title2 = mb_convert_encoding($title, 'UTF8', 'GBK');
        $this->mysql->insert('current', ['index' => $k, 'title'=>trim($title2)]);
        if(!$title){
            throw new Exception('文章内容丢失','101');
            return;
        }
        $con = $data->find('div#book_text', 0)->plaintext;
        //过滤不要的内容
        $con = str_replace('&nbsp;', '', $con);
        $con = str_replace('  ', '', $con);
        $con = str_replace('一秒记住【草莓小说网 www.cmxsw.Com 】，无弹窗，更新快，免费阅读！', '', $con);
        $con = str_replace('手机用户请浏览 http://m.cmxsw.Com阅读，更优质的阅读体验，书架与电脑版同步。', '', $con);
        $con = mb_convert_encoding($con, 'GBK', 'UTF8');
        $sec = $title . "\n\r\n\r" . $con . "\n\r\n\r\n\r";
        //给文件写入内容
        print_r("Writting start {$title} \n\r");
        fwrite($fp, $sec);
        print_r("Writting was done!\n\r");
    }

    protected function test() {
        $data = file_get_html('https://www.cmxsw.com/29/29766/21489372.html');
        $title = $data->find('div.book_content_text h1', 0)->innertext;
        $title = mb_convert_encoding($title, 'GBK', 'UTF8');
        $con = $data->find('div#book_text', 0)->innertext;
        $con = mb_convert_encoding($con, 'GBK', 'UTF8');
        $sec = $title . "\n\r\n\r" . $con . "\n\r\n\r\n\r";
        print_r($sec);
        die;
    }
}

$obj = new index();
$obj->main();