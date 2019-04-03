<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\PasswordReset;

class PasswordResetTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
    /**
     * write the test class to test the simple password reset
     * 
     * @retrun void
     */
    public function test_Forgot_Password_Create_Success(){
                
     $response = $this->json('POST', '/api/forgotpassword', [
     ]);
     $response->assertStatus(200);

    }
    // /**
    //  * write the test class to see the if user request the forgot password
    //  * 
    //  * @group forgotPassword
    //  * @return void
    //  */
    // public function test_ForgotPasswordCreateSuccess(){
    //     $user = factory(User::class)->create();

    //     $response = $this->withHeaders([
    //         'content-Type' =>'Application/json'
    //     ])->json('POST','/api/forgotpassword',[
    //        'email' => $user->email
    //     ]);

    //     $response->assertStatus(200)
    //     ->assertJsonCount(1)->assertExactJson(['message' => 'we have emailed you password reset link']);
    // }
     
    // /**
    //  * write the test class to see if the user cannot request the forgot password using email
    //  * 
    //  * @group forgotpassword
    //  * @return void
    //  */
    // public function test_ForgotPasswordCreateFailure(){
    //      $user = factory(User::class)->create();

    //      $response = $this->withHeaders([
    //          'Content-Type' => 'Application/Json'
    //      ])->json('Post','/api/forgotpassword',[
    //          'email' =>'anbhgj@gmail.com'
    //      ]);

    //      $response->assertStatus(200)->assertJsonCount(1)
    //      ->assertExactJson(
    //          ['message' => "We can't find a user with that email address."]);

    // }
}
