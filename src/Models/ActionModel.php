<?php

namespace Wealedger\Models;

/**
 * 接口详情类
 */
class ActionModel
{
    private $_rfMethod;

    /**
     * 接口方法名
     */
    private $_name;

    /**
     * 接口名称
     */
    private $_title;

    /**
     * 请求方法
     */
    private $_method;

    /**
     * 接口参数
     */
    private $_params = [];

    /**
     * 接口作者
     */
    private $_author;

    /**
     * 简介
     */
    private $_uses;

    /**
     * header头
     */
    private $_header = [];

    public function __construct(\ReflectionMethod $method)
    {
        $this->_rfMethod = $method;
        $this->init();
    }

    public function init()
    {
        $this->_name = $this->_rfMethod->name;

        $comment = $this->_rfMethod->getDocComment();


        if (preg_match_all('/@param\s*(.*)\n/', $comment, $matches) && !empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $params = preg_split("/[\s]+/", $match, 3);
                $info = explode('|', empty($params[0]) ? '' : $params[0]);
                $this->_params[] = [
                    'type' => empty($info[0]) ? '' : $info[0],
                    'is_necessary' => empty($info[1]) ? '' : $info[1],
                    'name' => isset($params[1]) ? str_replace('$', '', $params[1]) : '',
                    'desc' => empty($params[2]) ? '' : $params[2],
                ];
            }
        }

        if (preg_match('/@name\s*(.*)\n/', $comment, $matches) && !empty($matches[1])) {
            $this->_title = trim($matches[1], "\t\n\r\0\x0B");
        } else {
            $this->_title = $this->_rfMethod->name;
        }

        if (preg_match('/@method\s*(.*)\n/', $comment, $matches) && !empty($matches[1])) {
            $this->_method = trim($matches[1], "\t\n\r\0\x0B");
        } else {
            $this->_method = '';
        }

        if (preg_match('/@author\s*(.*)\n/', $comment, $matches) && !empty($matches[1])) {
            $this->_author = trim($matches[1], "\t\n\r\0\x0B");
        } else {
            $this->_author = '';
        }

        if (preg_match_all('/@header\s*(.*)\n/', $comment, $matches) && !empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $params = preg_split("/[\s]+/", $match, 3);
                $info = explode('|', empty($params[0]) ? '' : $params[0]);
                $this->_header[] = [
                    'type' => empty($info[0]) ? '' : $info[0],
                    'is_necessary' => empty($info[1]) ? '' : $info[1],
                    'name' => isset($params[1]) ? str_replace('$', '', $params[1]) : '',
                    'desc' => empty($params[2]) ? '' : $params[2],
                ];
            }
        }

        if (preg_match('/@uses\s*(.*)\n/', $comment, $matches) && !empty($matches[1])) {
            $this->_uses = trim($matches[1], "\t\n\r\0\x0B");
        } else {
            $this->_uses = '';
        }
    }

    public function name()
    {
        return $this->_name;
    }

    public function title()
    {
        return $this->_title;
    }

    public function method()
    {
        return strtoupper($this->_method);
    }

    public function setMethod($method)
    {
        $this->_method = strtoupper($method);
    }

    public function params()
    {
        return $this->_params;
    }

    public function header()
    {
        return $this->_header;
    }

    public function author()
    {
        return $this->_author;
    }

    public function uses()
    {
        return $this->_uses;
    }
}
