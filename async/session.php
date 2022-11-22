<?php
require('../init/config.php');
echo isset($_SESSION['csrf_token']) ? 1:0;
