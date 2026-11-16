<?php

namespace Tests\Feature;

use App\Models\User;
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
}

