<?php

namespace Tests\Feature;

use App\Models\BarangPersediaan;
use App\Models\KategoriBarang;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.gemini.api_key', 'test-gemini-fake-key');

        $ruangan = Ruangan::create([
            'kode_ruangan' => 'SOS',
            'nama_ruangan' => 'Fungsi Statistik Sosial',
            'penanggung_jawab' => 'Koordinator Sosial',
        ]);

        $this->user = User::factory()->create([
            'name' => 'Pegawai Uji Chat',
            'nip' => '199001012015011001',
            'role' => 'pegawai',
            'ruangan_id' => $ruangan->id,
            'jenis_kelamin' => 'L',
        ]);

        $kategori = KategoriBarang::create([
            'kode_kategori' => 'ATK',
            'nama_kategori' => 'Alat Tulis Kantor',
        ]);

        BarangPersediaan::create([
            'kode_barang' => '000001',
            'kode_sakti' => '1010301001',
            'kategori_id' => $kategori->id,
            'nama_barang' => 'Kertas HVS A4 80gr',
            'satuan' => 'Rim',
            'stok_minimum' => 5,
            'stok_aktif' => 25,
            'lokasi_rak' => 'Rak A-1',
        ]);
    }

    public function test_guest_can_send_chat_message_and_receive_response(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Halo! Kertas HVS A4 80gr saat ini tersedia sebanyak 25 Rim di Rak A-1.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->postJson(route('api.chat'), [
            'message' => 'Apakah ada kertas HVS A4?',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'reply' => 'Halo! Kertas HVS A4 80gr saat ini tersedia sebanyak 25 Rim di Rak A-1.',
            ]);
    }

    public function test_authenticated_user_can_chat_with_identity_context(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Halo Pak Pegawai Uji Chat, Anda dapat mengajukan permintaan ATK via menu Ajukan ATK.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->postJson(route('api.chat'), [
            'message' => 'Bagaimana alur pengajuan ATK saya?',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'name' => 'Pegawai Uji Chat',
                    'role' => 'pegawai',
                ],
            ]);
    }

    public function test_chat_validation_requires_message_and_checks_length(): void
    {
        $response = $this->postJson(route('api.chat'), [
            'message' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);

        $responseLong = $this->postJson(route('api.chat'), [
            'message' => str_repeat('a', 501),
        ]);

        $responseLong->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }

    public function test_chat_handles_api_failure_gracefully(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/*' => Http::response(null, 500),
        ]);

        $response = $this->postJson(route('api.chat'), [
            'message' => 'Cek ketersediaan spidol whiteboard',
        ]);

        $response->assertStatus(503)
            ->assertJson([
                'success' => false,
            ]);
    }
}
