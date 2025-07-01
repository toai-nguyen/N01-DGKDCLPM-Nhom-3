<?php

namespace Tests\Feature\Chapters;

use App\Models\Novel;
use App\Models\User;
use App\Models\Chapter;
use App\Notifications\NewChapterNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CreateChapterTest extends TestCase
{
    use RefreshDatabase;

    protected $author;
    protected $otherUser;
    protected $novel;
    protected $follower;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tạo author và users
        $this->author = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->follower = User::factory()->create();
        
        // Tạo novel test
        $this->novel = Novel::factory()->create([
            'author_id' => $this->author->id,
            'number_of_chapters' => 0,
        ]);

        // Thêm follower cho novel
        $this->novel->followers()->attach($this->follower->id);
    }

    public function test_create_chapter_page_can_be_displayed(): void
    {
        $response = $this
            ->actingAs($this->author)
            ->get(route('chapter.create', $this->novel->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Content/CreateChapter')
                 ->has('chapterNumber')
                 ->has('novelId')
                 ->where('novelId', $this->novel->id)
                 ->where('chapterNumber', 1) // First chapter
        );
    }

    public function test_create_chapter_page_shows_correct_chapter_number_for_first_chapter(): void
    {
        $response = $this
            ->actingAs($this->author)
            ->get(route('chapter.create', $this->novel->id));

        $response->assertInertia(fn ($page) => 
            $page->where('chapterNumber', 1)
        );
    }

    public function test_create_chapter_page_shows_correct_chapter_number_for_subsequent_chapters(): void
    {
        // Tạo một chapter trước đó
        Chapter::factory()->create([
            'novel_id' => $this->novel->id,
            'author_id' => $this->author->id,
            'chapter_number' => 1,
        ]);

        $response = $this
            ->actingAs($this->author)
            ->get(route('chapter.create', $this->novel->id));

        $response->assertInertia(fn ($page) => 
            $page->where('chapterNumber', 2) // Next chapter should be 2
        );
    }

    public function test_create_chapter_page_shows_correct_chapter_number_with_multiple_chapters(): void
    {
        // Tạo nhiều chapters
        Chapter::factory()->create([
            'novel_id' => $this->novel->id,
            'author_id' => $this->author->id,
            'chapter_number' => 1,
        ]);
        Chapter::factory()->create([
            'novel_id' => $this->novel->id,
            'author_id' => $this->author->id,
            'chapter_number' => 3,
        ]);
        Chapter::factory()->create([
            'novel_id' => $this->novel->id,
            'author_id' => $this->author->id,
            'chapter_number' => 2,
        ]);

        $response = $this
            ->actingAs($this->author)
            ->get(route('chapter.create', $this->novel->id));

        $response->assertInertia(fn ($page) => 
            $page->where('chapterNumber', 4) // Next should be 4 (3 + 1)
        );
    }

    public function test_guest_cannot_access_create_chapter_page(): void
    {
        $response = $this->get(route('chapter.create', $this->novel->id));

        $response->assertRedirect(route('login'));
    }

    public function test_create_chapter_page_for_nonexistent_novel_redirects_with_error(): void
    {
        $response = $this
            ->actingAs($this->author)
            ->get(route('chapter.create', 9999));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'Novel not found.');
    }

    public function test_chapter_can_be_created_successfully(): void
    {
        Notification::fake();

        $chapterData = [
            'title' => 'Test Chapter Title',
            'content' => 'This is the content of the test chapter with sufficient length for testing.',
            'novel_id' => $this->novel->id,
            'chapter_number' => 1,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $response->assertRedirect(route('view-novel', $this->novel->id));
        $response->assertSessionHas('success', 'Chapter created successfully.');

        // Kiểm tra chapter đã được tạo trong database
        $this->assertDatabaseHas('chapters', [
            'title' => 'Test Chapter Title',
            'content' => 'This is the content of the test chapter with sufficient length for testing.',
            'novel_id' => $this->novel->id,
            'author_id' => $this->author->id,
            'chapter_number' => 1,
        ]);

        // Kiểm tra novel's chapter count đã được cập nhật
        $this->novel->refresh();
        $this->assertEquals(1, $this->novel->number_of_chapters);

        // Kiểm tra notification đã được gửi
        Notification::assertSentTo(
            $this->follower,
            NewChapterNotification::class
        );
    }

    public function test_guest_cannot_create_chapter(): void
    {
        $chapterData = [
            'title' => 'Unauthorized Chapter',
            'content' => 'This should not be created',
            'novel_id' => $this->novel->id,
            'chapter_number' => 1,
        ];

        $response = $this->post(route('chapter.store', $this->novel->id), $chapterData);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseEmpty('chapters');
    }

    public function test_create_chapter_requires_title(): void
    {
        $chapterData = [
            'content' => 'Content without title',
            'novel_id' => $this->novel->id,
            'chapter_number' => 1,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseEmpty('chapters');
    }

    public function test_create_chapter_requires_content(): void
    {
        $chapterData = [
            'title' => 'Title without content',
            'novel_id' => $this->novel->id,
            'chapter_number' => 1,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $response->assertSessionHasErrors('content');
        $this->assertDatabaseEmpty('chapters');
    }

    public function test_create_chapter_requires_novel_id(): void
    {
        $chapterData = [
            'title' => 'Test Title',
            'content' => 'Test Content',
            'chapter_number' => 1,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $response->assertSessionHasErrors('novel_id');
        $this->assertDatabaseEmpty('chapters');
    }

    public function test_create_chapter_requires_chapter_number(): void
    {
        $chapterData = [
            'title' => 'Test Title',
            'content' => 'Test Content',
            'novel_id' => $this->novel->id,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $response->assertSessionHasErrors('chapter_number');
        $this->assertDatabaseEmpty('chapters');
    }

    public function test_create_chapter_requires_all_fields(): void
    {
        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), []);

        $response->assertSessionHasErrors(['title', 'content', 'novel_id', 'chapter_number']);
        $this->assertDatabaseEmpty('chapters');
    }

    public function test_create_chapter_validates_chapter_number_is_integer(): void
    {
        $chapterData = [
            'title' => 'Test Title',
            'content' => 'Test Content',
            'novel_id' => $this->novel->id,
            'chapter_number' => 'not-a-number',
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $response->assertSessionHasErrors('chapter_number');
        $this->assertDatabaseEmpty('chapters');
    }

    public function test_create_chapter_validates_chapter_number_minimum(): void
    {
        $chapterData = [
            'title' => 'Test Title',
            'content' => 'Test Content',
            'novel_id' => $this->novel->id,
            'chapter_number' => 0,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $response->assertSessionHasErrors('chapter_number');
        $this->assertDatabaseEmpty('chapters');
    }

    public function test_create_chapter_prevents_duplicate_chapter_numbers(): void
    {
        // Tạo chapter đầu tiên
        Chapter::factory()->create([
            'novel_id' => $this->novel->id,
            'author_id' => $this->author->id,
            'chapter_number' => 1,
        ]);

        // Thử tạo chapter với cùng số chapter
        $chapterData = [
            'title' => 'Duplicate Chapter',
            'content' => 'This should not be created',
            'novel_id' => $this->novel->id,
            'chapter_number' => 1,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $response->assertSessionHasErrors('chapter_number');
        $response->assertSessionHasErrorsIn('default', [
            'chapter_number' => 'Chapter number already exists for this novel.'
        ]);

        // Kiểm tra chỉ có 1 chapter trong database
        $this->assertEquals(1, Chapter::where('novel_id', $this->novel->id)->count());
    }

    public function test_create_chapter_for_nonexistent_novel_redirects_with_error(): void
    {
        $chapterData = [
            'title' => 'Test Title',
            'content' => 'Test Content',
            'novel_id' => 9999,
            'chapter_number' => 1,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', 9999), $chapterData);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'Novel not found.');
        $this->assertDatabaseEmpty('chapters');
    }

    public function test_create_chapter_increments_novel_chapter_count(): void
    {
        $initialCount = $this->novel->number_of_chapters;

        $chapterData = [
            'title' => 'Test Chapter',
            'content' => 'Test Content',
            'novel_id' => $this->novel->id,
            'chapter_number' => 1,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $this->novel->refresh();
        $this->assertEquals($initialCount + 1, $this->novel->number_of_chapters);
    }

    public function test_create_chapter_sends_notifications_to_followers(): void
    {
        Notification::fake();

        // Thêm thêm followers
        $follower2 = User::factory()->create();
        $this->novel->followers()->attach($follower2->id);

        $chapterData = [
            'title' => 'New Chapter',
            'content' => 'This will notify followers',
            'novel_id' => $this->novel->id,
            'chapter_number' => 1,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        // Kiểm tra notifications đã được gửi cho tất cả followers
        Notification::assertSentTo(
            [$this->follower, $follower2],
            NewChapterNotification::class
        );
    }

    public function test_create_chapter_with_empty_title(): void
    {
        $chapterData = [
            'title' => '',
            'content' => 'Valid content',
            'novel_id' => $this->novel->id,
            'chapter_number' => 1,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $response->assertSessionHasErrors('title');
        $this->assertDatabaseEmpty('chapters');
    }

    public function test_create_chapter_with_empty_content(): void
    {
        $chapterData = [
            'title' => 'Valid Title',
            'content' => '',
            'novel_id' => $this->novel->id,
            'chapter_number' => 1,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $response->assertSessionHasErrors('content');
        $this->assertDatabaseEmpty('chapters');
    }

    public function test_create_chapter_with_long_content(): void
    {
        $longContent = str_repeat('This is a very long chapter content. ', 1000);

        $chapterData = [
            'title' => 'Long Chapter',
            'content' => $longContent,
            'novel_id' => $this->novel->id,
            'chapter_number' => 1,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $response->assertRedirect(route('view-novel', $this->novel->id));
        $response->assertSessionHas('success', 'Chapter created successfully.');

        $this->assertDatabaseHas('chapters', [
            'title' => 'Long Chapter',
            'content' => $longContent,
            'novel_id' => $this->novel->id,
        ]);
    }

    public function test_create_chapter_sets_correct_author_id(): void
    {
        $chapterData = [
            'title' => 'Author Test Chapter',
            'content' => 'Testing author assignment',
            'novel_id' => $this->novel->id,
            'chapter_number' => 1,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.store', $this->novel->id), $chapterData);

        $this->assertDatabaseHas('chapters', [
            'title' => 'Author Test Chapter',
            'author_id' => $this->author->id,
            'novel_id' => $this->novel->id,
        ]);
    }
}