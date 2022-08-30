<?php
// htmlspecialcharの関数化
function hsc($value)
{
    return htmlspecialchars($value, ENT_QUOTES);
}

