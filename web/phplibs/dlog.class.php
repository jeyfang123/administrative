<?php
/**
* 封装Seaslog
* php必须安装Seaslog模块。
*/
class Dlog
{
	public function __construct()
    {
    }

    public function __destruct()
    {
    }

    /**
    *	设置basePath
    *   @param {string}		basePath	日志log根目录
    *   @return bool
    */
    static public function setBasePath ($basePath)
    {
        return SeasLog::setBasePath ($basePath);
    }


    /**
    *	获取basePath
    *   @return string
    */
    static public function getBasePath ()
    {
        return SeasLog::getBasePath ();
    }


    /**
    *	设置模块路径
    *   @param {string}		module	日志log模块路径
    *   @return bool
    */
    static public function setLogger ($module)
    {
        return SeasLog::setLogger ($module);
    }

	
	/**
    *	获取最后一次设置的模块目录
    *   @return string
    */
    static public function getLastLogger ()
    {
        return SeasLog::getLastLogger ();
    }


    /**
     * 统计所有类型（或单个类型）行数
     * @param {string}	$level
     * @param {string}	$log_path
     * @param {string}	$key_word
     * @return array | long
     */
    static public function analyzerCount ($level = 'all', $module = "default", $key_word = NULL)
    {
        SeasLog::setLogger ($module);
        return SeasLog::analyzerCount ($level, '*', $key_word);
    }

    /**
     * 以数组形式，快速取出某类型log的各行详情
     * @param $level
     * @param string $log_path
     * @param null $key_word
     * @param int $start
     * @param int $limit
     * @return array
     */
    static public function analyzerDetail ($level = SEASLOG_INFO, $module = "default", $key_word = NULL, $page = 1, $perpage = 20)
    {
        $start = ($page - 1) * $perpage + 1;
        $end  = $page * $perpage;

        SeasLog::setLogger ($module);
        return SeasLog::analyzerDetail ($level, '*', $key_word, $start, $end);
    }

    /**
     * 获得当前日志buffer中的内容
     * @return array
     */
    static public function getBuffer()
    {
        return SeasLog::getBuffer ();
    }

    /**
     * 记录debug日志
     * @param $message
     * @param array $content
     * @param string $module     
     *	例如：
     *	SeasLog::info ('this is a {userName} debug',array('{userName}' => 'neeke'), DLOG_MOD_USER);
     */
    static public function debug ($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_DEBUG
    	return SeasLog::debug ($message, $content, $module);
    }

    /**
     * 记录info日志
     * @param $message
     * @param array $content
     * @param string $module
     *	例如：
     *	SeasLog::info ('this is a {userName} debug',array('{userName}' => 'neeke'), DLOG_MOD_USER);
     */
    static public function info ($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_INFO
        return SeasLog::info ($message, $content, $module);
    }

    /**
     * 记录notice日志
     * @param $message
     * @param array $content
     * @param string $module
     *	例如：
     *	SeasLog::notice ('this is a {userName} debug',array('{userName}' => 'neeke'), DLOG_MOD_USER);
     */
    static public function notice ($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_NOTICE
        return SeasLog::notice ($message, $content, $module);
    }

    /**
     * 记录warning日志
     * @param $message
     * @param array $content
     * @param string $module
     *	例如：
     *	SeasLog::warning('this is a {userName} debug',array('{userName}' => 'neeke'), DLOG_MOD_USER);
     */
    static public function warning ($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_WARNING
        SeasLog::warning ($message, $content, $module);
    }

    /**
     * 记录error日志
     * @param $message
     * @param array $content
     * @param string $module
     *	例如：
     *	SeasLog::error('this is a {userName} debug',array('{userName}' => 'neeke'), DLOG_MOD_USER);
     */
    static public function error ($message, array $content = array(), $module = '')
    {
        #$level = SEASLOG_ERROR
        SeasLog::error ($message, $content, $module);
    }

}
?>