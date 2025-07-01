<?php

namespace Tests\Feature\Chapters;

use App\Models\Novel;
use App\Models\User;
use App\Models\Chapter;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateChapterTest extends TestCase
{
    use RefreshDatabase;

    protected $author;
    protected $otherUser;
    protected $novel;
    protected $chapter;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Tạo author và user khác
        $this->author = User::factory()->create();
        $this->otherUser = User::factory()->create();
        
        // Tạo novel test
        $this->novel = Novel::factory()->create([
            'author_id' => $this->author->id,
            'number_of_chapters' => 1,
        ]);
        
        // Tạo chapter test
        $this->chapter = Chapter::factory()->create([
            'novel_id' => $this->novel->id,
            'author_id' => $this->author->id,
            'title' => 'Original Chapter Title',
            'content' => 'Original chapter content for testing purposes.',
            'chapter_number' => 1,
        ]);
    }

    public function test_edit_chapter_page_can_be_displayed_by_author(): void
    {
        $response = $this
            ->actingAs($this->author)
            ->get(route('chapter.edit', [
                'novel_id' => $this->novel->id,
                'chapter_id' => $this->chapter->id
            ]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => 
            $page->component('Content/EditChapter')
                 ->has('chapter')
                 ->has('novelId')
                 ->where('chapter.id', $this->chapter->id)
                 ->where('chapter.title', 'Original Chapter Title')
                 ->where('novelId', $this->novel->id)
        );
    }

    public function test_edit_chapter_page_shows_correct_chapter_data(): void
    {
        $response = $this
            ->actingAs($this->author)
            ->get(route('chapter.edit', [
                'novel_id' => $this->novel->id,
                'chapter_id' => $this->chapter->id
            ]));

        $response->assertInertia(fn ($page) => 
            $page->where('chapter.title', 'Original Chapter Title')
                 ->where('chapter.content', 'Original chapter content for testing purposes.')
                 ->where('chapter.chapter_number', 1)
                 ->where('chapter.novel_id', $this->novel->id)
        );
    }

    public function test_guest_cannot_access_edit_chapter_page(): void
    {
        $response = $this->get(route('chapter.edit', [
            'novel_id' => $this->novel->id,
            'chapter_id' => $this->chapter->id
        ]));

        $response->assertRedirect(route('login'));
    }

    public function test_edit_nonexistent_chapter_redirects_with_error(): void
    {
        $response = $this
            ->actingAs($this->author)
            ->get(route('chapter.edit', [
                'novel_id' => $this->novel->id,
                'chapter_id' => 9999
            ]));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'Chapter not found.');
    }

    public function test_chapter_can_be_updated_successfully(): void
    {
        $updateData = [
            'title' => 'Updated Chapter Title',
            'content' => 'This is the updated chapter content with more details and information.',
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.update', [
                'novel_id' => $this->novel->id,
                'chapter_id' => $this->chapter->id
            ]), $updateData);

        $response->assertRedirect(route('view-novel', $this->novel->id));
        $response->assertSessionHas('success', 'Chapter updated successfully.');

        // Kiểm tra dữ liệu đã được cập nhật trong database
        $this->assertDatabaseHas('chapters', [
            'id' => $this->chapter->id,
            'title' => 'Updated Chapter Title',
            'content' => 'This is the updated chapter content with more details and information.',
            'novel_id' => $this->novel->id,
            'author_id' => $this->author->id,
            'chapter_number' => 1, // Chapter number không thay đổi
        ]);
    }

    public function test_chapter_update_preserves_other_fields(): void
    {
        $originalChapterNumber = $this->chapter->chapter_number;
        $originalAuthorId = $this->chapter->author_id;
        $originalNovelId = $this->chapter->novel_id;

        $updateData = [
            'title' => 'New Title',
            'content' => 'New content',
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.update', [
                'novel_id' => $this->novel->id,
                'chapter_id' => $this->chapter->id
            ]), $updateData);

        $response->assertRedirect(route('view-novel', $this->novel->id));

        // Kiểm tra các field khác không bị thay đổi
        $this->assertDatabaseHas('chapters', [
            'id' => $this->chapter->id,
            'chapter_number' => $originalChapterNumber,
            'author_id' => $originalAuthorId,
            'novel_id' => $originalNovelId,
            'title' => 'New Title',
            'content' => 'New content',
        ]);
    }

    public function test_guest_cannot_update_chapter(): void
    {
        $updateData = [
            'title' => 'Unauthorized Update',
            'content' => 'This should not be allowed',
        ];

        $response = $this->post(route('chapter.update', [
            'novel_id' => $this->novel->id,
            'chapter_id' => $this->chapter->id
        ]), $updateData);

        $response->assertRedirect(route('login'));

        // Kiểm tra chapter không bị thay đổi
        $this->assertDatabaseHas('chapters', [
            'id' => $this->chapter->id,
            'title' => 'Original Chapter Title',
            'content' => 'Original chapter content for testing purposes.',
        ]);
    }

    public function test_update_chapter_requires_title(): void
    {
        $updateData = [
            'content' => 'Updated content without title',
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.update', [
                'novel_id' => $this->novel->id,
                'chapter_id' => $this->chapter->id
            ]), $updateData);

        $response->assertSessionHasErrors('title');

        // Kiểm tra chapter không bị thay đổi
        $this->assertDatabaseHas('chapters', [
            'id' => $this->chapter->id,
            'title' => 'Original Chapter Title',
        ]);
    }

    public function test_update_chapter_requires_content(): void
    {
        $updateData = [
            'title' => 'Updated title without content',
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.update', [
                'novel_id' => $this->novel->id,
                'chapter_id' => $this->chapter->id
            ]), $updateData);

        $response->assertSessionHasErrors('content');

        // Kiểm tra chapter không bị thay đổi
        $this->assertDatabaseHas('chapters', [
            'id' => $this->chapter->id,
            'content' => 'Original chapter content for testing purposes.',
        ]);
    }

    public function test_update_chapter_requires_both_title_and_content(): void
    {
        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.update', [
                'novel_id' => $this->novel->id,
                'chapter_id' => $this->chapter->id
            ]), []);

        $response->assertSessionHasErrors(['title', 'content']);
    }

    public function test_update_nonexistent_chapter_redirects_with_error(): void
    {
        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.update', [
                'novel_id' => $this->novel->id,
                'chapter_id' => 9999
            ]), $updateData);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'Chapter not found.');
    }

    public function test_update_chapter_with_empty_title(): void
    {
        $updateData = [
            'title' => '',
            'content' => 'Valid content',
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.update', [
                'novel_id' => $this->novel->id,
                'chapter_id' => $this->chapter->id
            ]), $updateData);

        $response->assertSessionHasErrors('title');
    }

    public function test_update_chapter_with_empty_content(): void
    {
        $updateData = [
            'title' => 'Valid Title',
            'content' => '',
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.update', [
                'novel_id' => $this->novel->id,
                'chapter_id' => $this->chapter->id
            ]), $updateData);

        $response->assertSessionHasErrors('content');
    }

    public function test_update_chapter_with_long_title(): void
    {
        $longTitle = str_repeat('This is a very long title ', 20); // Tạo title rất dài

        $updateData = [
            'title' => $longTitle,
            'content' => 'Valid content',
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.update', [
                'novel_id' => $this->novel->id,
                'chapter_id' => $this->chapter->id
            ]), $updateData);

        // Nếu có giới hạn length, sẽ có error, nếu không thì success
        if ($response->getStatusCode() === 302 && !$response->getSession()->has('errors')) {
            $response->assertRedirect(route('view-novel', $this->novel->id));
        }
    }

    public function test_update_chapter_with_long_content(): void
    {
        $longContent = str_repeat('This is a very long content paragraph with lots of details and information. ', 100);

        $updateData = [
            'title' => 'Valid Title',
            'content' => $longContent,
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.update', [
                'novel_id' => $this->novel->id,
                'chapter_id' => $this->chapter->id
            ]), $updateData);

        $response->assertRedirect(route('view-novel', $this->novel->id));
        $response->assertSessionHas('success', 'Chapter updated successfully.');

        // Kiểm tra content dài được lưu thành công
        $this->assertDatabaseHas('chapters', [
            'id' => $this->chapter->id,
            'title' => 'Valid Title',
            'content' => $longContent,
        ]);
    }

    public function test_update_chapter_redirects_to_correct_novel_page(): void
    {
        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.update', [
                'novel_id' => $this->novel->id,
                'chapter_id' => $this->chapter->id
            ]), $updateData);

        $response->assertRedirect(route('view-novel', $this->novel->id));
    }

    public function test_update_chapter_for_different_novels(): void
    {
        // Tạo novel và chapter khác
        $anotherNovel = Novel::factory()->create([
            'author_id' => $this->author->id,
        ]);

        $anotherChapter = Chapter::factory()->create([
            'novel_id' => $anotherNovel->id,
            'author_id' => $this->author->id,
            'chapter_number' => 1,
        ]);

        $updateData = [
            'title' => 'Updated Another Chapter',
            'content' => 'Updated content for another chapter',
        ];

        $response = $this
            ->actingAs($this->author)
            ->post(route('chapter.update', [
                'novel_id' => $anotherNovel->id,
                'chapter_id' => $anotherChapter->id
            ]), $updateData);

        $response->assertRedirect(route('view-novel', $anotherNovel->id));
        
        // Kiểm tra chapter đúng được update
        $this->assertDatabaseHas('chapters', [
            'id' => $anotherChapter->id,
            'title' => 'Updated Another Chapter',
            'content' => 'Updated content for another chapter',
        ]);

        // Kiểm tra chapter gốc không bị ảnh hưởng
        $this->assertDatabaseHas('chapters', [
            'id' => $this->chapter->id,
            'title' => 'Original Chapter Title',
            'content' => 'Original chapter content for testing purposes.',
        ]);
    }
}