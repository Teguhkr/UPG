<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Download;
use App\Models\Faq;
use App\Models\MediaItem;
use App\Models\SiteSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportLegacyUpgCommand extends Command
{
    protected $signature = 'upg:import-legacy {--fresh : Truncate UPG tables before importing}';

    protected $description = 'Import legacy Joomla UPG KKP data (kkpupg_3) and media into UPG_New';

    public function handle(): int
    {
        if ($this->option('fresh')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Article::truncate();
            Faq::truncate();
            Download::truncate();
            MediaItem::truncate();
            Category::truncate();
            SiteSetting::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        $admin = $this->seedAdminAndSettings();
        $articleCatMap = $this->importArticleCategories();
        $this->importArticles($articleCatMap, $admin->id);
        $this->importFaqs();
        $downloadCatMap = $this->importDownloadCategories();
        $this->importDownloads($downloadCatMap);
        $this->importMediaItems();

        SiteSetting::clearCache();
        $this->info('Legacy UPG KKP data imported successfully!');

        return self::SUCCESS;
    }

    protected function seedAdminAndSettings(): User
    {
        $admin = User::updateOrCreate(
            ['username' => 'adminupg'],
            [
                'name' => 'Super User UPG',
                'email' => 'upg@kkp.go.id',
                'password' => Hash::make('admin123'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        $settings = [
            ['key' => 'site_name', 'value' => 'UPG KKP', 'group' => 'general', 'label' => 'Nama Situs', 'type' => 'text'],
            ['key' => 'site_tagline', 'value' => 'Unit Pengendalian Gratifikasi Kementerian Kelautan dan Perikanan RI', 'group' => 'general', 'label' => 'Subjudul / Tagline', 'type' => 'text'],
            ['key' => 'hero_title', 'value' => 'Wujudkan Integritas Kementerian Kelautan dan Perikanan Bebas Gratifikasi', 'group' => 'hero', 'label' => 'Judul Hero Beranda', 'type' => 'text'],
            ['key' => 'hero_subtitle', 'value' => 'Portal resmi Unit Pengendalian Gratifikasi (UPG) KKP sebagai pusat informasi, edukasi, konsultasi, dan pelaporan gratifikasi terintegrasi dengan GOL KPK RI.', 'group' => 'hero', 'label' => 'Deskripsi Hero Beranda', 'type' => 'textarea'],
            ['key' => 'office_name', 'value' => 'Unit Pengendalian Gratifikasi Kementerian Kelautan dan Perikanan', 'group' => 'contact', 'label' => 'Nama Unit', 'type' => 'text'],
            ['key' => 'office_address', 'value' => "Ruang Unit Pengendalian Gratifikasi\nGedung Mina Bahari II Lantai 5\nJl. Medan Merdeka Timur No. 16, Jakarta Pusat 10110", 'group' => 'contact', 'label' => 'Alamat Kantor', 'type' => 'textarea'],
            ['key' => 'contact_email', 'value' => 'upg@kkp.go.id', 'group' => 'contact', 'label' => 'Email Resmi', 'type' => 'text'],
            ['key' => 'contact_phone', 'value' => '(021) 3519070', 'group' => 'contact', 'label' => 'Telepon', 'type' => 'text'],
            ['key' => 'gol_url', 'value' => 'https://gol.kpk.go.id', 'group' => 'links', 'label' => 'URL Lapor Gratifikasi (GOL KPK)', 'type' => 'url'],
            ['key' => 'kpk_gratifikasi_url', 'value' => 'https://gratifikasi.kpk.go.id', 'group' => 'links', 'label' => 'URL Beranda Gratifikasi KPK', 'type' => 'url'],
            ['key' => 'kpk_simulasi_url', 'value' => 'https://gratifikasi.kpk.go.id/?page_id=3212', 'group' => 'links', 'label' => 'URL Simulasi Laporan Gratifikasi', 'type' => 'url'],
            ['key' => 'twitter_url', 'value' => 'https://twitter.com/upg_kkp', 'group' => 'links', 'label' => 'URL Twitter / X', 'type' => 'url'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/upg_kkp', 'group' => 'links', 'label' => 'URL Instagram', 'type' => 'url'],
            ['key' => 'youtube_url', 'value' => 'https://www.youtube.com/channel/UC97R16V2eiaVHaOvOy_kzKw', 'group' => 'links', 'label' => 'URL YouTube', 'type' => 'url'],
            ['key' => 'copyright_text', 'value' => 'Hak Cipta © ' . date('Y') . ' Unit Pengendalian Gratifikasi Kementerian Kelautan dan Perikanan RI. Gratifikasi Online (GOL) merupakan layanan resmi Komisi Pemberantasan Korupsi (KPK) RI.', 'group' => 'general', 'label' => 'Teks Hak Cipta Footer', 'type' => 'textarea'],
        ];

        foreach ($settings as $item) {
            SiteSetting::updateOrCreate(['key' => $item['key']], $item);
        }

        return $admin;
    }

    protected function importArticleCategories(): array
    {
        $legacyCats = DB::connection('legacy_joomla')
            ->table('categories')
            ->where('extension', 'com_content')
            ->where('published', 1)
            ->whereNotIn('id', [1, 10, 11, 15, 19]) // FAQ, Contoh Kasus, Infografis, Video have dedicated modules
            ->orderBy('lft')
            ->get();

        $map = [];
        foreach ($legacyCats as $i => $cat) {
            $slug = Str::slug($cat->alias ?: $cat->title);
            $record = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $cat->title,
                    'type' => 'article',
                    'description' => strip_tags((string) $cat->description) ?: null,
                    'sort_order' => $i + 1,
                    'is_published' => true,
                ]
            );
            $map[$cat->id] = $record->id;
        }

        return $map;
    }

    protected function importArticles(array $catMap, int $adminId): void
    {
        if (empty($catMap)) {
            return;
        }

        $legacyArticles = DB::connection('legacy_joomla')
            ->table('content')
            ->whereIn('catid', array_keys($catMap))
            ->where('state', 1)
            ->orderByDesc('created')
            ->get();

        foreach ($legacyArticles as $row) {
            $rawHtml = trim(($row->introtext ?? '') . "\n" . ($row->fulltext ?? ''));
            $cleanedHtml = $this->normalizeLegacyHtml($rawHtml);

            $featuredImage = $this->extractFeaturedImage($row->images ?? '', $cleanedHtml);
            $plainText = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($cleanedHtml))));
            $excerpt = Str::limit($plainText, 220);

            $slug = Str::slug($row->alias ?: $row->title);
            if (Article::where('slug', $slug)->exists()) {
                $slug .= '-' . $row->id;
            }

            $publishedAt = $this->parseLegacyDate($row->publish_up) ?? $this->parseLegacyDate($row->created) ?? now();

            Article::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $catMap[$row->catid],
                    'title' => $row->title,
                    'excerpt' => $excerpt,
                    'content' => $cleanedHtml,
                    'featured_image' => $featuredImage,
                    'is_featured' => (bool) $row->featured,
                    'is_published' => true,
                    'published_at' => $publishedAt,
                    'hits' => (int) $row->hits,
                    'sort_order' => (int) $row->ordering,
                    'author_id' => $adminId,
                    'meta_keywords' => $row->metakey ?: null,
                    'meta_description' => $row->metadesc ?: null,
                ]
            );
        }

        $this->info('Imported ' . count($legacyArticles) . ' articles.');
    }

    protected function importFaqs(): void
    {
        $rows = DB::connection('legacy_joomla')
            ->table('content')
            ->whereIn('catid', [10, 11])
            ->where('state', 1)
            ->orderBy('catid')
            ->orderBy('ordering')
            ->orderBy('id')
            ->get();

        foreach ($rows as $index => $row) {
            $type = $row->catid == 11 ? 'contoh_kasus' : 'faq';
            $slug = Str::slug($row->alias ?: $row->title);
            if (strlen($slug) > 180) {
                $slug = substr($slug, 0, 170) . '-' . $row->id;
            }
            if (Faq::where('slug', $slug)->exists()) {
                $slug .= '-' . $row->id;
            }

            $answer = $this->normalizeLegacyHtml(trim(($row->introtext ?? '') . "\n" . ($row->fulltext ?? '')));

            Faq::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_type' => $type,
                    'question' => $row->title,
                    'answer' => $answer,
                    'is_published' => true,
                    'sort_order' => $index + 1,
                    'hits' => (int) $row->hits,
                ]
            );
        }

        $this->info('Imported ' . count($rows) . ' FAQs & Contoh Kasus.');
    }

    protected function importDownloadCategories(): array
    {
        $cats = DB::connection('legacy_joomla')
            ->table('jdownloads_categories')
            ->where('id', '>', 1)
            ->orderBy('ordering')
            ->get();

        $map = [];
        foreach ($cats as $i => $cat) {
            if (! $cat->published && ! in_array($cat->id, [7, 8])) {
                continue;
            }
            $slug = 'dl-' . Str::slug($cat->alias ?: $cat->title);
            $record = Category::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $cat->title,
                    'type' => 'download',
                    'description' => strip_tags((string) ($cat->description ?? '')) ?: null,
                    'sort_order' => $i + 1,
                    'is_published' => true,
                ]
            );
            $map[$cat->id] = [
                'id' => $record->id,
                'cat_dir' => $cat->cat_dir,
            ];
        }

        return $map;
    }

    protected function importDownloads(array $catMap): void
    {
        $files = DB::connection('legacy_joomla')
            ->table('jdownloads_files')
            ->where('published', 1)
            ->orderBy('catid')
            ->orderBy('ordering')
            ->get();

        $allDiskFiles = Storage::disk('public')->allFiles('downloads');
        $byBasename = [];
        foreach ($allDiskFiles as $path) {
            $byBasename[basename($path)] = $path;
        }

        $imported = 0;
        foreach ($files as $row) {
            if (! isset($catMap[$row->catid])) {
                continue;
            }

            $catInfo = $catMap[$row->catid];
            $resolvedPath = null;
            $fileName = trim((string) $row->url_download);

            if ($fileName !== '') {
                $candidate1 = 'downloads/' . trim((string) $catInfo['cat_dir'], '/') . '/' . $fileName;
                $candidate2 = 'downloads/' . $fileName;
                if (Storage::disk('public')->exists($candidate1)) {
                    $resolvedPath = $candidate1;
                } elseif (Storage::disk('public')->exists($candidate2)) {
                    $resolvedPath = $candidate2;
                } elseif (isset($byBasename[$fileName])) {
                    $resolvedPath = $byBasename[$fileName];
                }
            }

            $externalUrl = trim((string) $row->extern_file) ?: null;
            if (! $resolvedPath && ! $externalUrl) {
                continue;
            }

            $fileSize = trim((string) $row->size);
            if ($resolvedPath && (! $fileSize || $fileSize === '0 B')) {
                $bytes = Storage::disk('public')->size($resolvedPath);
                $fileSize = $bytes > 1048576
                    ? round($bytes / 1048576, 2) . ' MB'
                    : round(max(1, $bytes) / 1024, 1) . ' KB';
            }

            $slug = Str::slug($row->alias ?: $row->title);
            if (strlen($slug) > 180) {
                $slug = substr($slug, 0, 170) . '-' . $row->id;
            }
            if (Download::where('slug', $slug)->exists()) {
                $slug .= '-' . $row->id;
            }

            $desc = trim(strip_tags(html_entity_decode((string) ($row->description ?: $row->description_long))));

            Download::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $catInfo['id'],
                    'title' => $row->title,
                    'description' => $desc ?: null,
                    'file_path' => $resolvedPath,
                    'file_name' => $fileName ?: null,
                    'file_size' => $fileSize ?: null,
                    'external_url' => $externalUrl,
                    'version' => trim((string) $row->release) ?: null,
                    'download_count' => (int) $row->downloads,
                    'is_published' => true,
                    'published_at' => $this->parseLegacyDate($row->publish_up) ?? $this->parseLegacyDate($row->created) ?? now(),
                ]
            );
            $imported++;
        }

        $this->info("Imported {$imported} download items.");
    }

    protected function importMediaItems(): void
    {
        // 1. Infografis files
        $infografisFiles = Storage::disk('public')->files('upg-media/konten/infografis');
        sort($infografisFiles);
        $i = 1;
        foreach ($infografisFiles as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                continue;
            }
            $base = pathinfo($file, PATHINFO_FILENAME);
            $title = Str::title(str_replace(['_', '-'], ' ', $base));
            MediaItem::updateOrCreate(
                ['slug' => 'infografis-' . Str::slug($base)],
                [
                    'type' => 'infografis',
                    'title' => $title,
                    'description' => 'Infografis Pengendalian Gratifikasi KKP',
                    'file_path' => $file,
                    'album' => 'Infografis PPG',
                    'sort_order' => $i++,
                    'is_published' => true,
                ]
            );
        }

        // 2. Videos from legacy modules & articles
        $videos = [
            [
                'slug' => 'video-stop-gratifikasi',
                'title' => 'Stop Gratifikasi',
                'description' => 'Tolak gratifikasi pada kesempatan pertama. Dibuat oleh Biro HKLN Setjen KKP.',
                'file_path' => 'upg-media/konten/videos/ILM_GRATIFIKASI_KKP.mp4',
                'poster_path' => 'upg-media/konten/videos/ilm-gratifikasi-kkp.png',
                'embed_url' => null,
                'sort_order' => 1,
            ],
            [
                'slug' => 'video-gratifikasi-kpk',
                'title' => 'Mengenal Gratifikasi (KPK RI)',
                'description' => 'Apa arti gratifikasi dan dampak yang ditimbulkannya? Dibuat oleh Komisi Pemberantasan Korupsi (KPK).',
                'file_path' => 'upg-media/konten/videos/video-gratifikasi-kpk.mp4',
                'poster_path' => 'upg-media/konten/videos/video-gratifikasi-kpk.png',
                'embed_url' => null,
                'sort_order' => 2,
            ],
            [
                'slug' => 'video-negative-list-gratifikasi',
                'title' => 'Negative List Gratifikasi',
                'description' => 'Kenali gratifikasi yang diperbolehkan (Gratifikasi yang Tidak Wajib Dilaporkan) bagi ASN agar terhindar dari tindak pidana korupsi.',
                'file_path' => 'upg-media/konten/videos/negative_list_720p.mp4',
                'poster_path' => 'upg-media/konten/videos/negative-list-cover.png',
                'embed_url' => null,
                'sort_order' => 3,
            ],
            [
                'slug' => 'video-elearning-gratifikasi',
                'title' => 'e-Learning Bimtek Pengendalian Gratifikasi',
                'description' => 'KPK membuka bimtek pengendalian gratifikasi dalam bentuk e-learning (online) yang dapat diakses dan diikuti oleh seluruh masyarakat.',
                'file_path' => 'upg-media/konten/videos/elearning-gratifikasi-720p.mp4',
                'poster_path' => 'upg-media/konten/videos/elearning-gratifikasi-cover.png',
                'embed_url' => null,
                'sort_order' => 4,
            ],
            [
                'slug' => 'video-perbedaan-gratifikasi-suap-pemerasan',
                'title' => 'Perbedaan Gratifikasi, Suap, dan Pemerasan',
                'description' => 'Video edukasi mengenai perbedaan mendasar antara gratifikasi, suap, dan pemerasan.',
                'file_path' => null,
                'poster_path' => 'upg-media/konten/videos/Perbedaan_Gratifikasi.png',
                'embed_url' => 'https://drive.google.com/file/d/155CAPG7Vuh1BC6pt1egZK9l3RiO7jjGn/preview',
                'sort_order' => 5,
            ],
        ];

        foreach ($videos as $v) {
            if ($v['file_path'] && ! Storage::disk('public')->exists($v['file_path'])) {
                $v['file_path'] = null;
            }
            if ($v['poster_path'] && ! Storage::disk('public')->exists($v['poster_path'])) {
                $v['poster_path'] = null;
            }
            MediaItem::updateOrCreate(
                ['slug' => $v['slug']],
                array_merge($v, [
                    'type' => 'video',
                    'album' => 'Video Gratifikasi',
                    'is_published' => true,
                ])
            );
        }

        // 3. Gallery photos (Diklat Integritas Itjen)
        $galleryFiles = Storage::disk('public')->files('upg-media/konten/berita/diklat-integritas-2021');
        sort($galleryFiles);
        $g = 1;
        foreach ($galleryFiles as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                continue;
            }
            $base = pathinfo($file, PATHINFO_FILENAME);
            MediaItem::updateOrCreate(
                ['slug' => 'gallery-' . Str::slug($base)],
                [
                    'type' => 'gallery',
                    'title' => 'Penanaman Nilai Integritas ASN Itjen #' . $g,
                    'description' => 'Dokumentasi kegiatan penanaman nilai-nilai integritas ASN di Inspektorat Jenderal KKP.',
                    'file_path' => $file,
                    'album' => 'Penanaman Nilai Integritas ASN Itjen',
                    'sort_order' => $g++,
                    'is_published' => true,
                ]
            );
        }

        $this->info('Imported media items (infografis, videos, gallery).');
    }

    protected function normalizeLegacyHtml(string $html): string
    {
        // Strip Joomla plugin tags like {loadmodule...}, {loadmoduleid...}, {plotalot...}
        $html = preg_replace('/\{(?:loadmodule|loadmoduleid|loadposition|plotalot)[^}]*\}/i', '', $html);

        // Rewrite relative images/ paths to /storage/upg-media/
        $html = preg_replace(
            '/(src|href)=["\']\/?images\/([^"\']+)["\']/i',
            '$1="/storage/upg-media/$2"',
            $html
        );

        return trim($html);
    }

    protected function extractFeaturedImage(string $imagesJson, string $cleanedHtml): ?string
    {
        $decoded = json_decode($imagesJson, true);
        if (is_array($decoded)) {
            $img = $decoded['image_intro'] ?: ($decoded['image_fulltext'] ?? '');
            if ($img && str_starts_with($img, 'images/')) {
                return 'upg-media/' . substr($img, strlen('images/'));
            }
        }

        if (preg_match('/<img[^>]+src=["\']\/storage\/(upg-media\/[^"\']+)["\']/i', $cleanedHtml, $m)) {
            return $m[1];
        }

        return null;
    }

    protected function parseLegacyDate(?string $date): ?Carbon
    {
        if (! $date || str_starts_with($date, '0000-00-00')) {
            return null;
        }

        try {
            return Carbon::parse($date);
        } catch (\Throwable) {
            return null;
        }
    }
}
