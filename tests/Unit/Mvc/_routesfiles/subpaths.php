<?php
return [
    '|^user/|' => [
        '|^([0-9]*)/|' => [
            '|^group/|' => [
                '|^([0-9]*)/|' => [
                    '|^edit/$|' => function($params){
                      return 'user:' . $params[0] . ':group:' . $params[1] . ':edit';
                    }
                ]
            ]
        ]
    ]
];