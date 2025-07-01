<?php

namespace Tests\Feature\Novels;

use App\Models\Novel;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CreateNovelTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $tags;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tạo user test
        $this->user = User::factory()->create();
        
        // Tạo một số tags test
        $this->tags = Tag::factory()->count(3)->create();
    }

    public function test_create_novel_page_can_be_displayed(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get(route('create-project'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Content/CreateProject')
                 ->has('tags')
        );
    }

    public function test_guest_cannot_access_create_novel_page(): void
    {
        $response = $this->get(route('create-project'));

        $response->assertRedirect(route('login'));
    }

    public function test_novel_can_be_created_successfully(): void
    {
        Storage::fake('public');
        
        $image = UploadedFile::fake()->image('novel.jpg', 600, 400);
        
        $novelData = [
            'title' => 'Test Novel Title',
            'description' => 'This is a test novel description that should be long enough to pass validation.',
            'tags' => $this->tags->pluck('id')->toArray(),
            'image' => $image,
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(route('add-novel'), $novelData);

        // Kiểm tra redirect
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Novel created successfully.');

        // Kiểm tra novel đã được tạo trong database
        $this->assertDatabaseHas('novels', [
            'title' => 'Test Novel Title',
            'description' => 'This is a test novel description that should be long enough to pass validation.',
            'author_id' => $this->user->id,
            'status' => 'ongoing',
            'followers' => 0,
            'number_of_chapters' => 0,
        ]);

        // Kiểm tra tags đã được attach
        $novel = Novel::where('title', 'Test Novel Title')->first();
        $this->assertCount(3, $novel->tags);
    }

    public function test_novel_creation_requires_authentication(): void
    {
        $novelData = [
            'title' => 'Test Novel Title',
            'description' => 'Test description',
            'tags' => $this->tags->pluck('id')->toArray(),
        ];

        $response = $this->post(route('add-novel'), $novelData);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseEmpty('novels');
    }

    public function test_novel_creation_requires_valid_data(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->post(route('add-novel'), []);

        $response->assertSessionHasErrors(['title', 'description', 'tags']);
        $this->assertDatabaseEmpty('novels');
    }

    public function test_novel_creation_requires_title(): void
    {
        $novelData = [
            'description' => 'Test description',
            'tags' => $this->tags->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(route('add-novel'), $novelData);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseEmpty('novels');
    }

    public function test_novel_creation_requires_description(): void
    {
        $novelData = [
            'title' => 'Test Novel Title',
            'tags' => $this->tags->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(route('add-novel'), $novelData);

        $response->assertSessionHasErrors('description');
        $this->assertDatabaseEmpty('novels');
    }

    public function test_novel_creation_requires_tags(): void
    {
        $novelData = [
            'title' => 'Test Novel Title',
            'description' => 'Test description',
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(route('add-novel'), $novelData);

        $response->assertSessionHasErrors('tags');
        $this->assertDatabaseEmpty('novels');
    }

    public function test_novel_creation_requires_image(): void
    {
        $novelData = [
            'title' => 'Test Novel Title',
            'description' => 'Test description',
            'tags' => $this->tags->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(route('add-novel'), $novelData);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseEmpty('novels');
    }

    public function test_novel_creation_validates_image_file_type(): void
    {
        Storage::fake('public');
        
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);
        
        $novelData = [
            'title' => 'Test Novel Title',
            'description' => 'Test description',
            'tags' => $this->tags->pluck('id')->toArray(),
            'image' => $invalidFile,
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(route('add-novel'), $novelData);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseEmpty('novels');
    }

    public function test_novel_creation_validates_image_size(): void
    {
        Storage::fake('public');
        
        // Tạo file ảnh lớn hơn 5MB (5012KB)
        $largeImage = UploadedFile::fake()->image('large.jpg')->size(6000);
        
        $novelData = [
            'title' => 'Test Novel Title',
            'description' => 'Test description',
            'tags' => $this->tags->pluck('id')->toArray(),
            'image' => $largeImage,
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(route('add-novel'), $novelData);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseEmpty('novels');
    }

    public function test_novel_creation_validates_title_length(): void
    {
        $novelData = [
            'title' => str_repeat('a', 256), // Vượt quá 255 ký tự
            'description' => 'Test description',
            'tags' => $this->tags->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(route('add-novel'), $novelData);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseEmpty('novels');
    }

    public function test_novel_creation_validates_empty_title(): void
    {
        $novelData = [
            'title' => '',
            'description' => 'Test description',
            'tags' => $this->tags->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(route('add-novel'), $novelData);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseEmpty('novels');
    }

    public function test_novel_creation_validates_empty_description(): void
    {
        $novelData = [
            'title' => 'Test Novel Title',
            'description' => '',
            'tags' => $this->tags->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(route('add-novel'), $novelData);

        $response->assertSessionHasErrors('description');
        $this->assertDatabaseEmpty('novels');
    }

    public function test_novel_creation_validates_empty_tags_array(): void
    {
        $novelData = [
            'title' => 'Test Novel Title',
            'description' => 'Test description',
            'tags' => [],
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(route('add-novel'), $novelData);

        $response->assertSessionHasErrors('tags');
        $this->assertDatabaseEmpty('novels');
    }
}

