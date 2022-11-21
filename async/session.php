<?php
require('../init/config.php');
echo isset($_SESSION['token']) ? 1:0;
