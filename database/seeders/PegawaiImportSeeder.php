<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Pegawai;
use App\Models\PengajuanAbsen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PegawaiImportSeeder extends Seeder
{
    public function run(): void
    {
        $csvPath = storage_path('app/import/pegawai.csv');
        if (!file_exists($csvPath)) {
            $this->command?->error("CSV not found at {$csvPath}");
            return;
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        }

        PengajuanAbsen::query()->delete();
        Pegawai::query()->delete();

        $rows = array_map('str_getcsv', file($csvPath));
        // Skip 3 header rows: title + column header + number header
        $dataRows = array_slice($rows, 3);

        // First pass: create all pegawai, store by CSV NO
        $pegawaiByCsvNo = [];

        foreach ($dataRows as $row) {
            if (count($row) < 8) continue;
            [$no, $nama, $nip, $pangkatGol, $jabatan, , , $atasanNo] = $row;

            if (!trim((string) $nip) || !trim((string) $nama)) continue;

            $csvNo = (int) $no;
            $pegawai = Pegawai::create([
                'nip'         => trim($nip),
                'nama'        => trim($nama),
                'pangkat_gol' => trim($pangkatGol),
                'jabatan'     => trim($jabatan),
                'bagian'      => $this->mapBagian($jabatan),
                'atasan_id'   => null,
            ]);

            $pegawaiByCsvNo[$csvNo] = [
                'model'     => $pegawai,
                'atasan_no' => $atasanNo ? (int) $atasanNo : null,
            ];
        }

        // Insert Widyanto (Kepala Balai) manually as NO=1 - head of organization
        $kepalaBalai = Pegawai::create([
            'nip'         => '197406122008121001',
            'nama'        => 'Widyanto Hendro Saputro, ST, M.Si',
            'pangkat_gol' => 'Pembina / IV A',
            'jabatan'     => 'Kepala Balai',
            'bagian'      => 'Pimpinan',
            'atasan_id'   => null,
        ]);

        // Map Widyanto as CSV NO = 1
        $pegawaiByCsvNo[1] = [
            'model'     => $kepalaBalai,
            'atasan_no' => 2, // Hasbiah per CSV
        ];

        // Second pass: resolve atasan_id via CSV NO reference
        foreach ($pegawaiByCsvNo as $csvNo => $entry) {
            if ($entry['atasan_no'] && isset($pegawaiByCsvNo[$entry['atasan_no']])) {
                $entry['model']->update([
                    'atasan_id' => $pegawaiByCsvNo[$entry['atasan_no']]['model']->id,
                ]);
            } elseif ($entry['atasan_no'] && $entry['atasan_no'] == $csvNo) {
                // Self-reference (Ilham NO=17) - set to null
                $entry['model']->update(['atasan_id' => null]);
            }
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        $this->command?->info('Imported ' . count($pegawaiByCsvNo) . ' pegawai (including Widyanto NO=1).');
    }

    private function mapBagian(string $jabatan): string
    {
        $jabatanLower = mb_strtolower($jabatan);

        if (str_contains($jabatanLower, 'kepala balai') || str_contains($jabatanLower, 'kepala instansi')) {
            return 'Pimpinan';
        }

        if (str_contains($jabatanLower, 'komputer') || str_contains($jabatanLower, 'teknologi informasi')) {
            return 'Teknologi Informasi';
        }

        return 'Tata Usaha';
    }
}