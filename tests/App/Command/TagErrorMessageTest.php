<?php

namespace Tests\App\Command;

use App\Command\TagErrorMessage;
use Common\Exception\TagBuddyException;
use PHPUnit\Framework\TestCase;

class TagErrorMessageTest extends TestCase
{
    /**
     * @test
     */
    function it_should_never_show_an_english_message_for_an_exception_it_does_not_know()
    {
        $message = TagErrorMessage::for(new TagBuddyException('Some internal English message'));

        $this->assertSame('Operazione non riuscita', $message);
    }
}
