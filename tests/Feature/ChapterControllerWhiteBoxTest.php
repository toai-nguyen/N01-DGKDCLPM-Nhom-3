<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Novel;
use App\Models\Chapter;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChapterControllerWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    private $novel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->novel = Novel::factory()->create(['status' => 'ongoing']);
    }

    // 1. STATEMENT COVERAGE
    public function test_statement_coverage()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('chapter.create', $this->novel->id));
        $response->assertStatus(200)
                 ->assertInertia(fn ($page) =>
                    $page->component('Content/CreateChapter') // Đúng tên component
                         ->has('chapterNumber')
                         ->has('novelId')
                 );
    }

    // 2. BRANCH COVERAGE
    public function test_branch_novel_not_found()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('chapter.create', 999999));
        $response->assertRedirect(route('home'));
    }

    public function test_branch_novel_found()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('chapter.create', $this->novel->id));
        $response->assertStatus(200);
    }

    // 3. CONDITION COVERAGE
    public function test_condition_status_valid()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->novel->update(['status' => 'ongoing']);
        $response = $this->get(route('chapter.create', $this->novel->id));
        $response->assertStatus(200);
    }

    public function test_condition_status_invalid()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->novel->update(['status' => 'completed']);
        $response = $this->get(route('chapter.create', $this->novel->id));
        // Nếu controller trả về 403 khi truy cập novel completed
        $response->assertStatus(403);
    }

    // 4. PATH COVERAGE
    public function test_path_no_chapters()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('chapter.create', $this->novel->id));
        $response->assertInertia(fn ($page) => $page->where('chapterNumber', 1));
    }

    public function test_path_with_chapters()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Chapter::factory()->count(3)->create(['novel_id' => $this->novel->id]);
        $response = $this->get(route('chapter.create', $this->novel->id));
        $response->assertInertia(fn ($page) => $page->where('chapterNumber', 4));
    }

    // 5. LOOP TESTING
    public function test_loop_zero_iterations()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('chapter.create', $this->novel->id));
        $response->assertInertia(fn ($page) => $page->where('chapterNumber', 1));
    }

    public function test_loop_multiple_iterations()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Chapter::factory()->count(5)->create(['novel_id' => $this->novel->id]);
        $response = $this->get(route('chapter.create', $this->novel->id));
        $response->assertInertia(fn ($page) => $page->where('chapterNumber', 6));
    }

    public function test_loop_large_dataset()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Chapter::factory()->count(100)->create(['novel_id' => $this->novel->id]);
        $response = $this->get(route('chapter.create', $this->novel->id));
        $response->assertInertia(fn ($page) => $page->where('chapterNumber', 101));
    }

    // 6. GUEST REDIRECT
    public function test_guest_redirect_create_chapter()
    {
        // Không đăng nhập user nào
        $response = $this->get(route('chapter.create', $this->novel->id));
        // Đảm bảo đúng route login, thường là /login
        $response->assertRedirect(route('login'));
    }

    // 7. VALIDATION ERROR
    public function test_create_chapter_missing_title()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('chapter.store', $this->novel->id), [
            'content' => 'No title',
            'novel_id' => $this->novel->id,
            'chapter_number' => 1,
        ]);
        // Đảm bảo assertSessionHasErrors nhận mảng
        $response->assertSessionHasErrors(['title']);
    }
}