<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\Article;
use App\Models\User;
use App\Models\Category;

class PostTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    protected function authenticate()
    {
        $user = User::create([
            'name' => 'test',
            'email' => rand(12345,678910).'test@gmail.com',
            'password' => \Hash::make('secret1234'),
        ]);

        if (!auth()->attempt(['email'=>$user->email, 'password'=>'secret1234'])) {
            return response(['message' => 'Login credentials are invaild']);
        }

        return $accessToken = auth()->user()->createToken('authToken')->accessToken;
    }

    public function test_create_category()
    {
        $token = $this->authenticate();

        $res = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', 'api/v1/category', [
            'name' => 'Laravel'
        ]);

        $res->assertStatus(201);
    }

    public function test_create_post()
    {
        $token = $this->authenticate();

        Storage::fake('image');
        $file = UploadedFile::fake()->image('avatar.jpg');
        
        $res = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('POST', 'api/v1/posts', [
            'title' => 'test title',
            'content' => 'test content',
            'image' => $file,
            'user_id' => 1,
            'category_id' => 1,
        ]);

        $res->assertStatus(201);
    }

    public function test_update_post()
    {
        $token = $this->authenticate();

        Storage::fake('image');
        $file = UploadedFile::fake()->image('avatar.jpg');
        
        $res = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('PUT', 'api/v1/posts/11', [
            'title' => 'test title update',
            'content' => 'test content update',
            'image' => $file,
            'user_id' => 1,
            'category_id' => 1,
        ]);

        $res->assertStatus(200);
    }

    public function test_detail_post()
    {
        $token = $this->authenticate();

        $res = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', 'api/v1/posts/11');

        $res->assertStatus(200);
    }

    public function test_get_all_post()
    {
        $token = $this->authenticate();

        $res = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('GET', 'api/v1/posts');

        $res->assertStatus(200);
    }

    public function test_delete_post()
    {
        $token = $this->authenticate();

        $res = $this->withHeaders([
            'Authorization' => 'Bearer '. $token,
        ])->json('DELETE', 'api/v1/posts/11');

        $res->assertStatus(200);
    }
}

