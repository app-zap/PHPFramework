<?php

return [
    '.' => function() { return 'index'; },
    '|^user/(.*)$|' => function($params) { return 'user:' . $params[0]; },
];