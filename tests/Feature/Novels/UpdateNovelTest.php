<?php

namespace Tests\Feature\Novels;

use App\Models\Novel;
use App\Models\User;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpdateNovelTest extends TestCase
{
    use RefreshDatabase;

    protected $author;
    protected $otherUser;
    protected $novel;
    protected $tags;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tạo author và user khác
        $this->author = User::factory()->create();
        $this->otherUser = User::factory()->create();
        
        // Tạo tags
        $this->tags = Tag::factory()->count(4)->create();
        
        // Tạo novel test
        $this->novel = Novel::factory()->create([
            'author_id' => $this->author->id,
            'title' => 'Original Title',
            'description' => 'Original Description',
            'status' => 'ongoing',
        ]);
        
        // Attach tags
        $this->novel->tags()->attach($this->tags->take(2)->pluck('id'));
    }

    public function test_edit_novel_page_can_be_displayed_by_author(): void
    {
        $response = $this
            ->actingAs($this->author)
            ->get(route('edit-novel', $this->novel->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Content/EditProject')
                 ->has('novel')
                 ->has('tags')
        );
    }

    public function test_edit_novel_page_cannot_be_accessed_by_non_author(): void
    {
        $response = $this
            ->actingAs($this->otherUser)
            ->get(route('edit-novel', $this->novel->id));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'You do not have permission to edit this novel.');
    }

    public function test_guest_cannot_access_edit_novel_page(): void
    {
        $response = $this->get(route('edit-novel', $this->novel->id));

        $response->assertRedirect(route('login'));
    }

    public function test_novel_can_be_updated_successfully_without_image(): void
    {
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
            'tags' => $this->tags->take(3)->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertRedirect(route('view-novel', $this->novel->id));
        $response->assertSessionHas('success', 'Novel updated successfully.');

        // Kiểm tra dữ liệu đã được cập nhật
        $this->assertDatabaseHas('novels', [
            'id' => $this->novel->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
        ]);

        // Kiểm tra tags đã được cập nhật
        $updatedNovel = Novel::findOrFail($this->novel->id);
        $this->assertCount(3, $updatedNovel->tags);
    }

    public function test_novel_can_be_updated_with_new_image(): void
    {
        Storage::fake('public');
        
        $image = UploadedFile::fake()->image('updated.jpg', 600, 400);
        
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
            'tags' => $this->tags->take(2)->pluck('id')->toArray(),
            'image' => $image,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertRedirect(route('view-novel', $this->novel->id));
        $response->assertSessionHas('success', 'Novel updated successfully.');

        // Kiểm tra dữ liệu đã được cập nhật
        $this->assertDatabaseHas('novels', [
            'id' => $this->novel->id,
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
        ]);
    }

    public function test_only_author_can_update_novel(): void
    {
        $updateData = [
            'title' => 'Malicious Update',
            'description' => 'Malicious Description',
            'status' => 'completed',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->otherUser)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'You do not have permission to edit this novel.');

        // Kiểm tra dữ liệu KHÔNG được cập nhật
        $this->assertDatabaseHas('novels', [
            'id' => $this->novel->id,
            'title' => 'Original Title',
            'description' => 'Original Description',
        ]);
    }

    public function test_guest_cannot_update_novel(): void
    {
        $updateData = [
            'title' => 'Guest Update',
            'description' => 'Guest Description',
            'status' => 'completed',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
        ];

        $response = $this->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertRedirect(route('login'));
    }

    public function test_update_novel_requires_valid_data(): void
    {
        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), []);

        $response->assertSessionHasErrors(['title', 'description', 'status', 'tags']);
    }

    public function test_update_novel_requires_title(): void
    {
        $updateData = [
            'description' => 'Updated Description',
            'status' => 'completed',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasErrors('title');
    }

    public function test_update_novel_requires_description(): void
    {
        $updateData = [
            'title' => 'Updated Title',
            'status' => 'completed',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasErrors('description');
    }

    public function test_update_novel_requires_status(): void
    {
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasErrors('status');
    }

    public function test_update_novel_requires_tags(): void
    {
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasErrors('tags');
    }

    public function test_update_novel_validates_title_length(): void
    {
        $updateData = [
            'title' => str_repeat('a', 256), // Vượt quá 255 ký tự
            'description' => 'Updated Description',
            'status' => 'completed',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasErrors('title');
    }

    public function test_update_novel_validates_empty_title(): void
    {
        $updateData = [
            'title' => '',
            'description' => 'Updated Description',
            'status' => 'completed',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasErrors('title');
    }

    public function test_update_novel_validates_empty_description(): void
    {
        $updateData = [
            'title' => 'Updated Title',
            'description' => '',
            'status' => 'completed',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasErrors('description');
    }

    public function test_update_novel_validates_status_values(): void
    {
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'invalid_status',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasErrors('status');
    }

    public function test_update_novel_validates_empty_tags_array(): void
    {
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
            'tags' => [],
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasErrors('tags');
    }

    public function test_update_novel_validates_image_file_type(): void
    {
        Storage::fake('public');
        
        $invalidFile = UploadedFile::fake()->create('document.pdf', 1000);
        
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
            'image' => $invalidFile,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasErrors('image');
    }

    public function test_update_novel_validates_image_size(): void
    {
        Storage::fake('public');
        
        // Tạo file ảnh lớn hơn 5MB (5012KB)
        $largeImage = UploadedFile::fake()->image('large.jpg')->size(6000);
        
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
            'image' => $largeImage,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasErrors('image');
    }

    public function test_update_nonexistent_novel_returns_404(): void
    {
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', 9999), $updateData);

        $response->assertStatus(404);
    }

    public function test_update_novel_syncs_tags_correctly(): void
    {
        // Novel ban đầu có 2 tags
        $this->assertCount(2, $this->novel->tags);

        // Cập nhật với 3 tags khác
        $newTags = $this->tags->skip(1)->take(3)->pluck('id')->toArray();
        
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
            'tags' => $newTags,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertRedirect(route('view-novel', $this->novel->id));

        // Kiểm tra tags đã được sync
        $updatedNovel = Novel::findOrFail($this->novel->id);
        $this->assertCount(3, $updatedNovel->tags);
        
        // Kiểm tra tags cụ thể
        $updatedTagIds = $updatedNovel->tags->pluck('id')->toArray();
        $this->assertEquals(sort($newTags), sort($updatedTagIds));
    }

    public function test_update_novel_accepts_valid_status_values(): void
    {
        // Test với status 'ongoing'
        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'ongoing',
            'tags' => $this->tags->take(1)->pluck('id')->toArray(),
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasNoErrors();

        // Test với status 'completed'
        $updateData['status'] = 'completed';
        
        $response = $this
            ->actingAs($this->author)
            ->post(route('update-novel', $this->novel->id), $updateData);

        $response->assertSessionHasNoErrors();
    }
}