<?php

namespace Funcoes\Lib\Funcoes;

class Image
{
    protected $_src;
    protected $_title;
    private $_align = 'left';
    private $_vertical_align = 'middle';
    private $_param;
    private $_height;
    protected $_width;
    protected $_path = '/assets/img/';
    protected $_inactive = false;
    protected $_style = '';
    private $_link = '';

    public function __construct($src, $title = '')
    {
        $this->_src = $src;
        $this->_title = $title;
        return $this;
    }

    public function setStyle($value)
    {
        $this->_style = $value;
        return $this;
    }

    public function setLink($link)
    {
        $this->_link = $link;
        return $this;
    }

    public function setSrc($value)
    {
        $this->_src = $value;
        return $this;
    }

    public function setTitle($value)
    {
        $this->_title = $value;
        return $this;
    }

    public function setAlign($value)
    {
        $this->_align = $value;
        return $this;
    }

    public function setVerticalAlign($value)
    {
        $this->_vertical_align = $value;
        return $this;
    }

    public function setParam($value)
    {
        $this->_param = $value;
        return $this;
    }

    public function setPath($value)
    {
        $this->_path = $value;
        return $this;
    }

    public function setInactive($bool)
    {
        $this->_inactive = $bool;
        return $this;
    }

    public function setHeight($value)
    {
        $this->_height = $value;
        return $this;
    }

    public function setWidth($value)
    {
        $this->_width = $value;
        return $this;
    }

    public function html()
    {
        $class = "";
        if ($this->_inactive)
            $class .= " imgInativo";

        $style = '';
        if ($this->_style != '')
            $style = (substr(trim($this->_style), -1) == ';') ? $this->_style : $this->_style . ';';

        $style .= "align:{$this->_align}; ";

        $style .= "vertical-align:{$this->_vertical_align}; ";

        if ($this->_height > 0 && $this->_width > 0)
            $style .= "max-height: {$this->_height}px; max-width: {$this->_width}px; ";
        elseif ($this->_height > 0)
            $style .= "height: {$this->_height}px; width: auto; ";
        elseif ($this->_width > 0)
            $style .= "height: auto; width: {$this->_width}px; ";

        if ($style != '')
            $style = "style='{$style}'";

        $str = '';

        if ($this->_link != '') {
            $str = '<a href="' . $this->_link . '">';
        }

        $str .= "<img src='{$this->_path}{$this->_src}' {$style} {$this->_param} title='{$this->_title}' border='0' class='{$class}'>";

        if ($this->_link != '') {
            $str .= '</a>';
        }

        return $str;
    }
}
