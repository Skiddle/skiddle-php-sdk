<?php

/**
 * Tests for session stuff
 */
class SkiddleSessionTest extends PHPUnit_Framework_TestCase
{

    public $sample_api_key = '01234567890';
    public $api_key = '';

    public function testSetApiKey($key = '01234567890') {
        $this->assertNotEmpty($key);
        $this->assertEquals($key,$this->sample_api_key);
        $this->api_key = $this->sample_api_key;
    }

    function testGetApiKey() {
        $this->assertNotEmpty($this->api_key);
    }

    /**
     * Determine whether to enable debugging output etc.
     * @param boolean $mode Whether to enable or not
     */
    public function testSetDebugMode($mode = false) {
        if (!$mode || !isset($mode)) {
            $this->dev_mode = false;
        } else {
            $this->dev_mode = true;
        }
    }
    
}
