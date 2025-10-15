<?php
require 'config/constants.php';

// Minden munkamenet törlése és vissza a főoldalra
session_destroy();
header('location:' . ROOT_URL);
die();