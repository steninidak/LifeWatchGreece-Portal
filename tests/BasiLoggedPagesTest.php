<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Models\GuiUser;

class BasicLoggedPagesTest extends TestCase
{    
    use DatabaseTransactions;
    
    /** @test */
    public function homePage() {
        
        $user = GuiUser::find(1);   
        $this->be($user);
        $this->visit('/home')->see('Home Page');
    }
    
    /** @test */
    public function profilePage() {
        
        $user = GuiUser::find(1);   
        $this->be($user);
        $this->visit('/profile')->see('xayate@yahoo.com');
    }
    
    /** @test */
     public function contactUsPage() {
        
        $user = GuiUser::find(1);   
        $this->be($user);
        $this->visit('/contact_us')->see('Contact Form');
    }
    
    /** @test */
    public function announcementsPage() {
        $user = GuiUser::find(1);
        $this->be($user);
        $this->visit('/admin/announcements')->see('Active Announcements');
    }
    
}