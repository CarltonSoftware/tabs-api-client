<?php

$file = dirname(__FILE__) 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . '..' 
    . DIRECTORY_SEPARATOR . 'tabs' 
    . DIRECTORY_SEPARATOR . 'autoload.php';
require_once $file;

class CustomerCommentClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test a new address object
     * 
     * @return void 
     */
    public function testCommentObject()
    {
        $comment = new \tabs\api\property\CustomerComment(
            new \DateTime(),
            'Alex',
            'Superb property!'
        );
        $this->assertEquals('Alex', $comment->getName());
        $this->assertEquals('Superb property!', $comment->getComment());
        $this->assertEquals(new \DateTime(), $comment->getDate());
        $this->assertEquals('Alex - Superb property!', (string) $comment);
    }
}
