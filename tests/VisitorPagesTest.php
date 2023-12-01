<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\GuiUser;

class VisitorPagesTest extends TestCase
{    
    use DatabaseTransactions;
    
    /** @test */
    public function landingPage() {
        $this->visit('/')->see('Forgot your password');
    }        
    
    /** @test */
    public function registrationPage() {
        $this->visit('/register')->see('Affiliation');
    } 
    
    /** @test */
    public function captchaLink() {
        $response = $this->visit('/new_captcha_link');
        $this->assertStringStartsWith('https://portal.lifewatchgreece.eu/captcha',$response);
    } 
}
