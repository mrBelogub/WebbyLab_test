<?php

$id = $_GET["id"];

Validator::isEmpty("ID фільму", $id);

Movie::delete($id);
