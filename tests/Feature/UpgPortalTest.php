<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Faq;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpgPortalTest extends TestCase
{
    public function test_public_portal_pages_return_200(): void
    {
        $routes = [
            '/',
            '/profil/tentang-upg',
            '/profil/hubungi-upg-kkp',
            '/publikasi/informasi',
            '/publikasi/mengenal-gratifikasi',
            '/publikasi/infografis',
            '/publikasi/video-gratifikasi',
            '/unduh',
            '/faq',
            '/faq/contoh-kasus',
            '/cari?q=gratifikasi',
            '/halaman/kebijakan-privasi',
            '/halaman/ketentuan-penggunaan',
        ];

        foreach ($routes as $uri) {
            $response = $this->get($uri);
            $response->assertStatus(200, "Failed asserting 200 for {$uri}");
        }
    }

    public function test_admin_pages_require_authentication_and_work_when_logged_in(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/login')->assertStatus(200);

        $admin = User::where('username', 'adminupg')->firstOrFail();

        $adminRoutes = [
            '/admin',
            '/admin/articles',
            '/admin/articles/create',
            '/admin/categories',
            '/admin/faqs',
            '/admin/faqs/create',
            '/admin/downloads',
            '/admin/downloads/create',
            '/admin/media',
            '/admin/media/create',
            '/admin/inquiries',
            '/admin/users',
            '/admin/settings',
        ];

        foreach ($adminRoutes as $uri) {
            $response = $this->actingAs($admin)->get($uri);
            $response->assertStatus(200, "Failed asserting 200 for admin route {$uri}");
        }
    }

    public function test_admin_theme_switcher_has_the_javascript_hook(): void
    {
        $admin = User::where('username', 'adminupg')->firstOrFail();

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('dropdown-style-switcher');
    }

    public function test_only_super_admins_can_access_user_management_and_site_settings(): void
    {
        $user = User::factory()->create([
            'username' => 'role-test-' . uniqid(),
            'role' => 'editor',
        ]);

        try {
            $this->actingAs($user)
                ->get('/admin')
                ->assertOk()
                ->assertDontSee(route('admin.users.index'), false)
                ->assertDontSee(route('admin.settings.index'), false)
                ->assertDontSee('Pengaturan Situs');

            $this->get('/admin/users')->assertForbidden();
            $this->post('/admin/users')->assertForbidden();
            $this->get('/admin/settings')->assertForbidden();
            $this->put('/admin/settings')->assertForbidden();
        } finally {
            $user->delete();
        }
    }

    public function test_article_cover_image_is_displayed_on_detail_page(): void
    {
        Storage::fake('public');

        $admin = User::where('username', 'adminupg')->firstOrFail();
        $category = Category::where('type', 'article')->firstOrFail();
        $slug = 'test-cover-image-' . uniqid();

        try {
            $this->actingAs($admin)->post('/admin/articles', [
                'category_id' => $category->id,
                'title' => 'Artikel Uji Gambar Sampul',
                'slug' => $slug,
                'content' => '<p>Konten uji.</p>',
                'is_published' => '1',
                'featured_image_file' => UploadedFile::fake()->image('cover.jpg', 800, 600),
            ])->assertRedirect('/admin/articles');

            $article = Article::where('slug', $slug)->firstOrFail();
            $this->assertNotNull($article->featured_image);
            Storage::disk('public')->assertExists($article->featured_image);
            $this->actingAs($admin)->get('/admin/articles/' . $article->id . '/edit')
                ->assertOk()
                ->assertSee('Hapus');

            $this->get('/artikel/' . $article->slug)
                ->assertStatus(200)
                ->assertSee($article->image_url, false);

            $oldImage = $article->featured_image;
            $this->actingAs($admin)->put('/admin/articles/' . $article->id, [
                'category_id' => $category->id,
                'title' => 'Artikel Uji Gambar Sampul Edited',
                'slug' => $slug,
                'content' => '<p>Konten uji diperbarui.</p>',
                'is_published' => '1',
                'featured_image_file' => UploadedFile::fake()->image('cover-new.jpg', 800, 600),
            ])->assertRedirect('/admin/articles');

            $article->refresh();
            $this->assertNotSame($oldImage, $article->featured_image);
            Storage::disk('public')->assertMissing($oldImage);
            Storage::disk('public')->assertExists($article->featured_image);

            $this->get('/artikel/' . $article->slug)
                ->assertStatus(200)
                ->assertSee($article->image_url, false);

            $imageToRemove = $article->featured_image;
            $this->actingAs($admin)->delete('/admin/articles/' . $article->id . '/featured-image')
                ->assertRedirect();

            $article->refresh();
            $this->assertNull($article->featured_image);
            Storage::disk('public')->assertMissing($imageToRemove);
        } finally {
            Article::where('slug', $slug)->delete();
        }
    }

    public function test_faq_and_case_sort_order_shifts_cleanly_on_create_and_update(): void
    {
        $admin = User::where('username', 'adminupg')->firstOrFail();

        DB::beginTransaction();

        try {
            $firstFaq = Faq::where('category_type', 'faq')->orderBy('sort_order')->orderBy('id')->first();
            $initialCount = Faq::where('category_type', 'faq')->count();

            // Create a new FAQ at sort_order = 1; it should take priority 1 and shift existing items down
            $this->actingAs($admin)->post('/admin/faqs', [
                'category_type' => 'faq',
                'question' => 'FAQ Prioritas Baru Test',
                'answer' => '<p>Jawaban uji prioritas.</p>',
                'sort_order' => 1,
                'is_published' => '1',
            ])->assertRedirect('/admin/faqs');

            $created = Faq::where('question', 'FAQ Prioritas Baru Test')->firstOrFail();
            $this->assertSame(1, (int) $created->sort_order);

            if ($firstFaq) {
                $firstFaq->refresh();
                $this->assertSame(2, (int) $firstFaq->sort_order);
            }

            $orders = Faq::where('category_type', 'faq')
                ->orderBy('sort_order')
                ->pluck('sort_order')
                ->map(fn ($v) => (int) $v)
                ->all();
            $this->assertSame(range(1, $initialCount + 1), $orders);

            // Create another FAQ at sort_order = 2 and then edit the first to move to sort_order = 2
            $this->actingAs($admin)->post('/admin/faqs', [
                'category_type' => 'faq',
                'question' => 'FAQ Kedua Test',
                'answer' => '<p>Jawaban kedua.</p>',
                'sort_order' => 2,
                'is_published' => '1',
            ])->assertRedirect('/admin/faqs');

            $secondCreated = Faq::where('question', 'FAQ Kedua Test')->firstOrFail();
            $this->assertSame(2, (int) $secondCreated->sort_order);
            if ($firstFaq) {
                $firstFaq->refresh();
                $this->assertSame(3, (int) $firstFaq->sort_order);
            }

            // Edit secondCreated from sort_order = 2 to sort_order = 1 -> created shifts to 2
            $this->actingAs($admin)->put('/admin/faqs/' . $secondCreated->id, [
                'category_type' => 'faq',
                'question' => 'FAQ Kedua Test',
                'answer' => '<p>Jawaban kedua.</p>',
                'sort_order' => 1,
                'is_published' => '1',
            ])->assertRedirect('/admin/faqs');

            $secondCreated->refresh();
            $created->refresh();
            $this->assertSame(1, (int) $secondCreated->sort_order);
            $this->assertSame(2, (int) $created->sort_order);

            $updatedOrders = Faq::where('category_type', 'faq')
                ->orderBy('sort_order')
                ->pluck('sort_order')
                ->map(fn ($v) => (int) $v)
                ->all();
            $this->assertSame(range(1, $initialCount + 2), $updatedOrders);
        } finally {
            DB::rollBack();
        }
    }
}

