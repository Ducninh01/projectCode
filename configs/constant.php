<?php

const IN_ACTIVE = 0;
const ACTIVE = 1;
const DELETE_ACTIVE = 2;

function getCurrentURl()
{

    return substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
}
?>
