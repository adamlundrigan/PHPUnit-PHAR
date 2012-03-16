#!/usr/bin/env php
<?php
/* PHPUnit
 *
 * Copyright (c) 2002-2010, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRIC
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

function __autoload($class) {
    require_once implode(DIRECTORY_SEPARATOR, array(
        dirname(__FILE__),
        str_replace(array('_','\\'),DIRECTORY_SEPARATOR, $class)
    )) . '.php'; 
}

require 'PHPUnit/Util/FilterIterator.php';

$stub = <<<ENDSTUB
#!/usr/bin/env php
<?php

function __autoload(\$class) {
    if (substr(\$class,0,8) == 'PHPUnit_') {
        require_once 'phar://' . implode(DIRECTORY_SEPARATOR, array(
            __FILE__,
            str_replace(array('_','\\\'), DIRECTORY_SEPARATOR, \$class)
        )) . '.php'; 
    }
}

Phar::mapPhar();
PHPUnit_TextUI_Command::main();
__HALT_COMPILER();
ENDSTUB;

$phar = new Phar('output/phpunit.phar', 0, 'phpunit.phar');
$phar->startBuffering();
 
$phar->buildFromIterator(
  new PHPUnit_Util_FilterIterator(
    new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator('PHPUnit')
    )
  ),
  dirname(__FILE__) # . DIRECTORY_SEPARATOR . 'PHPUnit'
);

$phar->setStub($stub);
$phar->stopBuffering();
?>
