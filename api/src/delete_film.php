<?php

$id = $_GET["id"];

Validator::isEmpty("ID фільму", $id);

Films::delete($id);