<?php

namespace App\Services;

use App\Models\Submission;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;

class WordExportService
{
    /**
     * Path to the Word template .docx file.
     *
     * @var string
     */
    protected $templatePath;

    /**
     * Create a new instance.
     *
     * @param  string  $templatePath  Path to the .docx template
     */
    public function __construct($templatePath = null)
    {
        if (! $templatePath) {
            // Default template location: 3-page Susenas template
            $templatePath = resource_path('templates/report_template.docx');
        }
        $this->templatePath = $templatePath;
    }

    /**
     * Get the template path.
     */
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Generate a bulk Word document for multiple submissions.
     *
     * @param  \Illuminate\Support\Collection|Submission[]  $submissions  Each submission must be loaded with: 'activity', 'respondents'
     * @return string Path to the generated temporary file
     *
     * @throws \Exception If template not found
     */
    public function generateBulk($submissions)
    {
        if (! file_exists($this->templatePath)) {
            throw new \Exception("Word template not found at: {$this->templatePath}\n\nPlease create the Susenas 3-page template with the required block and placeholders.");
        }
        
        $template = new TemplateProcessor($this->templatePath);
        $debug = $template->getVariables();
        $submissions = collect($submissions);
        $totalBlocks = $submissions->count();
        // dd($totalBlocks, $submissions->pluck('id'), $template->getVariables());

        if ($totalBlocks === 0) {
            throw new \Exception('No submissions provided for bulk export.');
        }

        // Clone the main block for each submission (also removes the block markers)
        $template->cloneBlock('block_laporan', $totalBlocks, true, true);

        foreach ($submissions as $idx => $submission) {
            if ($idx == 1){
                // dd($template->getVariables(),$debug);
            }
            $blockNum = $idx + 1;
            $blockSuffix = '#' . $blockNum;
            $activity = $submission->activity;
            $respondents = $submission->respondents;
            $respondentCount = $respondents->count();
            // dd($template->getVariables(),$debug);

            // --- Simple (block-level) placeholders ---
            $simple = [
                'title' => $activity->title ?? '',
                'nama_mitra' => $submission->nama_mitra ?? '',
                'kec_sls' => $respondents->pluck('kec_sls')->filter()->unique()->implode(', '),
                'desa_sls' => $respondents->pluck('desa_sls')->filter()->unique()->implode(', '),
                'provinsi' => $activity->provinsi ?? '',
                'kabupaten' => $activity->kabupaten ?? '',
                'nama_pemeriksa' => $activity->nama_pemeriksa ?? '',
                'nks_gabungan' => $respondents->pluck('nks_resp')->filter()->unique()->implode(', '),
                'wilayah_gabungan' => $respondents->pluck('nama_sls')->filter()->unique()->implode(', '),
            ];

            foreach ($simple as $key => $value) {
                // Akan mengisi: title#1, title#2, dst
                $template->setValue($key . $blockSuffix, $value);
            }

            // --- Table 1: Respondents (Page 1) ---
            $nks_gabungan = $respondents->pluck('nks_resp')->filter()->unique()->implode(', ');
            $wilayah_gabungan = $respondents->pluck('nama_sls')->filter()->unique()->implode(', ');
            $template->setValue('nks_gabungan' . $blockSuffix, $nks_gabungan);
            $template->setValue('wilayah_gabungan' . $blockSuffix, $wilayah_gabungan);
            // dd($template->getVariables());
            if ($respondentCount > 0) {
                $base = 'no' . $blockSuffix; 
                
                // 1. Clone barisnya
                $template->cloneRow($base, $respondentCount); 

                foreach ($respondents as $i => $resp) {
                    $rowNum = $i + 1;
                    // PHPWord secara default menghasilkan format: TAG_BLOCK#ROW
                    // Jadi: no_1#1, no_1#2, dst.
                    $rowSuffix = '#' . $rowNum;
                    
                    $template->setValue('no' . $blockSuffix . $rowSuffix, (string) $rowNum);
                    $template->setValue('nama_resp' . $blockSuffix . $rowSuffix, $resp->nama_resp ?? '');
                    $template->setValue('nks_resp' . $blockSuffix . $rowSuffix, $resp->nks_resp ?? '');
                }
            }

            // dd($template->getVariables());

            // --- Table 2: SLS Grouping (Page 2) ---
            $groupedSls = $respondents->groupBy('nama_sls');
            $sortedSlsNames = $groupedSls->keys()->sort()->values();
            $groupCount = $groupedSls->count();

            if ($groupCount > 0) {
                $baseField = 'no_sls' . $blockSuffix;
                // Clone row sebanyak jumlah grup
                $template->cloneRow($baseField, $groupCount);

                foreach ($sortedSlsNames as $idx => $slsName) {
                    $rowNum = $idx + 1;
                    // Format suffix wajib menggunakan # untuk baris hasil clone
                    $suffix = '#' . $rowNum; 
                    
                    $template->setValue('no_sls' . $blockSuffix . $suffix, (string) $rowNum);
                    
                    $first = $groupedSls[$slsName]->first();
                    $template->setValue('kec_sls' . $blockSuffix . $suffix, $first->kec_sls ?? '');
                    $template->setValue('desa_sls' . $blockSuffix . $suffix, $first->desa_sls ?? '');
                    $template->setValue('nama_sls' . $blockSuffix . $suffix, $slsName);
                }
            }

            // --- Table 3: Photos (Page 3) ---

            $photoRowCount = (int) ceil($respondentCount / 2);

            if ($photoRowCount > 0) {
                $baseField = 'nama_kiri' . $blockSuffix;
                $template->cloneRow($baseField, $photoRowCount);

                for ($k = 0; $k < $photoRowCount; $k++) {
                    $rowNum = $k + 1;
                    $suffix = '#' . $rowNum; 
                    
                    $leftIdx = $k * 2;
                    $rightIdx = $leftIdx + 1;

                    // Nama tag yang akan dicari di dokumen
                    $dummy = 'dummy'. $blockSuffix . $suffix;
                    $tagNamaKiri = 'nama_kiri' . $blockSuffix . $suffix;
                    $tagFotoKiri = 'foto_kiri' . $blockSuffix . $suffix;
                    $tagWilayahTugasKiri = 'wilayah_tugas_kiri' . $blockSuffix . $suffix;
                    $tagNamaKanan = 'nama_kanan' . $blockSuffix . $suffix;
                    $tagFotoKanan = 'foto_kanan' . $blockSuffix . $suffix;
                    $tagWilayahTugasKanan = 'wilayah_tugas_kanan' . $blockSuffix . $suffix;

                    // --- Left respondent ---
                    if ($leftIdx < $respondentCount) {
                        $leftResp = $respondents[$leftIdx];
                        $template->setValue($tagNamaKiri, $leftResp->nama_resp ?? '');
                        $template->setValue($tagWilayahTugasKiri, $leftResp->nama_sls ?? '');
                        
                        if (!empty($leftResp->photo_path)) {
                            $fullPath = storage_path('app/public/' . $leftResp->photo_path);
                            // dd($tagFotoKiri, $fullPath);
                            if (file_exists($fullPath)) {
                                // Gunakan 'path', dan pastikan height diisi 
                                // karena baris pertama sering butuh kepastian ukuran
                                // if($k == 0) {
                                // $template->setImageValue($dummy, $fullPath);
                                // }
                                $template->setImageValue($tagFotoKiri, [
                                    'path'  => $fullPath,
                                    'width' => '189pt',
                                    'height' => '',         // 5 cm x 37.8 px = 189 px
                                    'ratio' => true         // Membuat tinggi gambar menyesuaikan secara otomatis (proporsional)
                                ]);
                            } else {
                                $template->setValue($tagFotoKiri, ''); // Hapus tag jika file fisik tidak ada
                            }
                        } else {
                            $template->setValue($tagFotoKiri, '');
                        }
                    }

                    // --- Right respondent ---
                    if ($rightIdx < $respondentCount) {
                        $rightResp = $respondents[$rightIdx];
                        $template->setValue($tagNamaKanan, $rightResp->nama_resp ?? '');
                        $template->setValue($tagWilayahTugasKanan, $rightResp->nama_sls ?? '');
                        
                        if (!empty($rightResp->photo_path)) {
                            $fullPath = storage_path('app/public/' . $rightResp->photo_path);
                            if (file_exists($fullPath)) {
                                // dd($tagFotoKanan, $fullPath);
                                $template->setImageValue($tagFotoKanan, [
                                    'path'  => $fullPath,
                                    'width' => '189pt',         // 5 cm x 37.8 px = 189 px
                                    'height' => '',         // 5 cm x 37.8 px = 189 px
                                    'ratio' => true         // Membuat tinggi gambar menyesuaikan secara otomatis (proporsional)
                                ]);
                            } else {
                                $template->setValue($tagFotoKanan, '');
                            }
                        } else {
                            $template->setValue($tagFotoKanan, '');
                        }
                    } else {
                        // Bersihkan tag jika data kanan kosong (misal total responden ganjil)
                        $template->setValue($tagNamaKanan, '');
                        $template->setValue($tagFotoKanan, '');
                    }
                }
            }
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'susenas_bulk_') . '.docx';
        $template->saveAs($tempFile);

        return $tempFile;
    }
}
