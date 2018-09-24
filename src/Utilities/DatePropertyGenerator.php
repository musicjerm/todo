<?php

namespace App\Utilities;

class DatePropertyGenerator
{
    /** @var int */
    public $daysDiff;

    /** $var string */
    public $string;

    /** @var string */
    public $color;

    /** @var string */
    public $icon;

    public function __construct(int $daysDiff)
    {
        $this->daysDiff = $daysDiff;

        $this
            ->setString()
            ->setColor()
            ->setIcon();
    }

    public function setString(): self
    {
        $this->string = 'Target completion date ';

        if ($this->daysDiff > 1){
            $this->string .= "is in $this->daysDiff days";
        }elseif ($this->daysDiff === 1){
            $this->string .= 'is tomorrow!';
        }elseif ($this->daysDiff === 0){
            $this->string .= 'is today!';
        }elseif ($this->daysDiff === -1){
            $this->string .= 'was yesterday!';
        }else{
            $this->string .= 'was ' . $this->daysDiff *-1 . ' days ago';
        }

        return $this;
    }

    public function setColor(): self
    {
        if ($this->daysDiff > 7){
            $this->color = 'blue';
        }elseif ($this->daysDiff > 1){
            $this->color = 'orange';
        }else{
            $this->color = 'red';
        }

        return $this;
    }

    public function setIcon(): self
    {
        if ($this->daysDiff > 7){
            $this->icon = 'info-circle';
        }else{
            $this->icon = 'warning';
        }

        return $this;
    }
}