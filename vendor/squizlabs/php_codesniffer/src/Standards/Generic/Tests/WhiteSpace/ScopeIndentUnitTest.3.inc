<?php
// phpcs:set Generic.WhiteSpace.ScopeIndent tabIndent false
// phpcs:set Generic.WhiteSpace.ScopeIndent exact true
function test()
{
     echo 'test';
     echo 'test2';
    echo 'test3';
    if (true) {
         echo 'test3';
    }
    echo 'test3';
    $x = f1(
        'test1', 'test2',
        'test3'
    );
}

if ($foo) {
    [
      'enabled'     => $enabled,
      'compression' => $compression,
    ] = $options;
}

$this->foo()
    ->bar()
    ->baz();

// Issue squizlabs/PHP_CodeSniffer#3808
function test() {
    yield
        from [ 3, 4 ];
}
