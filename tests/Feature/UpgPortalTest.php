<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\UploadedFile;
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
        } finally {
            Article::where('slug', $slug)->delete();
        }
    }
}

