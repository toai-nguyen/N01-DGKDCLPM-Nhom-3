<?php

namespace Tests\Feature\Novels;

use App\Models\Novel;
use App\Models\User;
use App\Models\Tag;
use App\Models\Chapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetByIdNovelTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $author;
    protected $novel;
    protected $tags;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tạo author và user khác
        $this->author = User::factory()->create();
        $this->user = User::factory()->create();
        
        // Tạo tags
        $this->tags = Tag::factory()->count(2)->create();
        
        // Tạo novel test
        $this->novel = Novel::factory()->create([
            'author_id' => $this->author->id,
            'title' => 'Test Novel',
            'description' => 'Test Description',
            'status' => 'ongoing',
            'followers' => 5,
            'number_of_chapters' => 2,
        ]);
        
        // Attach tags
        $this->novel->tags()->attach($this->tags->pluck('id'));
        
        // Tạo chapters
        Chapter::factory()->count(2)->create([
            'novel_id' => $this->novel->id,
            'author_id' => $this->author->id,
        ]);
    }

    public function test_novel_detail_page_can_be_displayed(): void
    {
        $response = $this->get(route('view-novel', $this->novel->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Content/ProjectDetail')
                 ->has('novel')
                 ->has('isAuthor')
        );
    }

    public function test_guest_can_view_novel_detail(): void
    {
        $response = $this->get(route('view-novel', $this->novel->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->where('isAuthor', false)
                 ->has('novel')
        );
    }

    public function test_authenticated_user_can_view_novel_detail(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get(route('view-novel', $this->novel->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->where('isAuthor', false)
                 ->has('novel')
        );
    }

    public function test_author_can_view_own_novel_detail(): void
    {
        $response = $this
            ->actingAs($this->author)
            ->get(route('view-novel', $this->novel->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->where('isAuthor', true)
                 ->has('novel')
        );
    }

    public function test_novel_detail_contains_correct_data(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get(route('view-novel', $this->novel->id));

        $response->assertInertia(fn ($page) => 
            $page->where('novel.id', $this->novel->id)
                 ->where('novel.title', 'Test Novel')
                 ->where('novel.description', 'Test Description')
                 ->where('novel.status', 'ongoing')
                 ->where('novel.author_id', $this->author->id)
                 ->where('isAuthor', false)
        );
    }

    public function test_novel_detail_includes_chapters(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get(route('view-novel', $this->novel->id));

        $response->assertInertia(fn ($page) => 
            $page->has('novel.chapters')
                 ->where('novel.chapters', fn ($chapters) => 
                     count($chapters) === 2
                 )
        );
    }

    public function test_novel_detail_includes_tags(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get(route('view-novel', $this->novel->id));

        $response->assertInertia(fn ($page) => 
            $page->has('novel.tags')
                 ->where('novel.tags', fn ($tags) => 
                     count($tags) === 2
                 )
        );
    }

    public function test_novel_detail_includes_author_info(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get(route('view-novel', $this->novel->id));

        $response->assertInertia(fn ($page) => 
            $page->has('novel.author')
                 ->where('novel.author.id', $this->author->id)
                 ->where('novel.author.name', $this->author->name)
        );
    }

    public function test_viewing_nonexistent_novel_returns_error(): void
    {
        $nonExistentId = 99999;
        
        $response = $this->get(route('view-novel', $nonExistentId));

        // Kiểm tra xem có redirect về home không hoặc trả về 404
        // Tùy thuộc vào cách implement trong repository
        $this->assertTrue(
            $response->status() === 404 || 
            $response->isRedirect()
        );
    }

    public function test_viewing_novel_with_invalid_id_format(): void
    {
        $response = $this->get('/novels/invalid-id');

        // Laravel sẽ trả về 404 cho route param không hợp lệ
        $response->assertStatus(404);
    }

    public function test_novel_detail_shows_correct_chapter_count(): void
    {
        // Tạo thêm chapters
        Chapter::factory()->count(3)->create([
            'novel_id' => $this->novel->id,
            'author_id' => $this->author->id,
        ]);

        $response = $this
            ->actingAs($this->user)
            ->get(route('view-novel', $this->novel->id));

        $response->assertInertia(fn ($page) => 
            $page->where('novel.chapters', fn ($chapters) => 
                count($chapters) === 5 // 2 từ setUp + 3 mới tạo
            )
        );
    }

    public function test_novel_detail_shows_chapters_in_correct_order(): void
    {
        // Tạo chapters với chapter_number cụ thể
        Chapter::factory()->create([
            'novel_id' => $this->novel->id,
            'author_id' => $this->author->id,
            'chapter_number' => 3,
            'title' => 'Chapter 3'
        ]);

        Chapter::factory()->create([
            'novel_id' => $this->novel->id,
            'author_id' => $this->author->id,
            'chapter_number' => 1,
            'title' => 'Chapter 1'
        ]);

        $response = $this
            ->actingAs($this->user)
            ->get(route('view-novel', $this->novel->id));

        $response->assertInertia(fn ($page) => 
            $page->has('novel.chapters')
                 // Kiểm tra chapters được sắp xếp theo thứ tự
                 ->where('novel.chapters.chapter_number', 1)
        );
    }

    public function test_is_author_flag_correctly_set_for_different_users(): void
    {
        // Test với author
        $authorResponse = $this
            ->actingAs($this->author)
            ->get(route('view-novel', $this->novel->id));

        $authorResponse->assertInertia(fn ($page) => 
            $page->where('isAuthor', true)
        );

        // Test với user khác
        $userResponse = $this
            ->actingAs($this->user)
            ->get(route('view-novel', $this->novel->id));

        $userResponse->assertInertia(fn ($page) => 
            $page->where('isAuthor', false)
        );

        // Test với guest
        $guestResponse = $this->get(route('view-novel', $this->novel->id));

        $guestResponse->assertInertia(fn ($page) => 
            $page->where('isAuthor', false)
        );
    }

    public function test_novel_detail_includes_image_data(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get(route('view-novel', $this->novel->id));

        $response->assertInertia(fn ($page) => 
            $page->has('novel.image_url')
                 ->has('novel.image_public_id')
        );
    }

    public function test_novel_detail_includes_follower_count(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get(route('view-novel', $this->novel->id));

        $response->assertInertia(fn ($page) => 
            $page->where('novel.followers', 5)
        );
    }

    public function test_novel_detail_includes_status(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get(route('view-novel', $this->novel->id));

        $response->assertInertia(fn ($page) => 
            $page->where('novel.status', 'ongoing')
        );
    }
}